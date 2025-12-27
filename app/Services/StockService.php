<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Refer;
use App\Models\Movement;
use App\Models\Article;
use App\Models\Stockcenter;
use App\Notifications\Notificationalert;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;

class StockService
{
    public function filterStocks(
        ?string $stockcenterId,
        ?string $type,
        ?string $articleName,
        ?string $code
    ): Collection {
        $query = Stock::query();

        if ($stockcenterId && $stockcenterId !== '*') {
            $query->where('id_stockcenter', $stockcenterId);
        }

        if ($articleName) {
            $articleIds = Article::where('name', 'LIKE', "%{$articleName}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }

        if ($type) {
            $articleIds = Article::where('type', 'LIKE', "%{$type}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }

        if ($code) {
            $articleIds = Article::where('code', 'LIKE', "%{$code}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }

        return $query->with(['article', 'stockCenter'])
            ->orderBy('id_stockcenter')
            ->orderBy('id_article')
            ->get();
    }

    public function paginateStocks(
        ?string $stockcenterId,
        ?string $type,
        ?string $articleName,
        ?string $code,
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = Stock::query();

        if ($stockcenterId && $stockcenterId !== '*') {
            $query->where('id_stockcenter', $stockcenterId);
        }

        if ($articleName) {
            $articleIds = Article::where('name', 'LIKE', "%{$articleName}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }

        if ($type) {
            $articleIds = Article::where('type', 'LIKE', "%{$type}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }

        if ($code) {
            $articleIds = Article::where('code', 'LIKE', "%{$code}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }

        return $query->with(['article', 'stockCenter'])
            ->orderBy('id_stockcenter')
            ->orderBy('id_article')
            ->paginate($perPage);
    }

    public function findStock(int $id): Stock
    {
        return Stock::with(['article', 'stockCenter'])->findOrFail($id);
    }

    public function getStockMovements(int $articleId, int $stockcenterId, int $perPage = 20): LengthAwarePaginator
    {
        return Movement::where('id_article', $articleId)
            ->whereHas('refer', function($query) use ($stockcenterId) {
                $query->where('origen_id_stockcenter', $stockcenterId)
                      ->orWhere('destiny_id_stockcenter', $stockcenterId);
            })
            ->with(['refer', 'article'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    public function updateQuantityAlert(int $stockId, int $quantityAlert): Stock
    {
        $stock = Stock::findOrFail($stockId);
        $stock->quantity_alert = $quantityAlert;
        $stock->save();

        $this->updateStockAlert($stock);

        return $stock;
    }

    public function adjustStock(Refer $refer, Collection $movements, bool $increase): void
    {
        $stockcenterField = $increase ? 'destiny_id_stockcenter' : 'origen_id_stockcenter';
        $stockcenterId = $refer->$stockcenterField;

        foreach ($movements as $movement) {
            // Actualizar estado de tránsito del movimiento
            if ($increase) {
                $movement->markAsOutTransit();
            } else {
                $movement->markAsInTransit();
            }

            // Actualizar stock
            $stock = Stock::firstOrNew([
                'id_stockcenter' => $stockcenterId,
                'id_article' => $movement->id_article,
            ]);

            if ($increase) {
                $stock->quantity += $movement->quantity;
            } else {
                $stock->quantity -= $movement->quantity;
            }

            if ($stock->quantity < 0) {
                $stock->quantity = 0;
            }

            $stock->save();

            // Actualizar alertas
            $this->updateStockAlert($stock, !$increase);
        }
    }

    public function updateStockAlert(Stock $stock, bool $decreaseMode = false): void
    {
        $user = Auth::user();
        
        if (!$user) {
            return;
        }

        // Buscar notificaciones existentes
        $existingNotification = $user->notifications->first(function ($notification) use ($stock) {
            $data = $notification->data;
            return isset($data['stockcenter_id']) && 
                   isset($data['article_id']) &&
                   $data['stockcenter_id'] == $stock->id_stockcenter && 
                   $data['article_id'] == $stock->id_article;
        });

        // Si el stock está por encima de la alerta y hay notificación, eliminarla
        if ($stock->quantity > $stock->quantity_alert && $existingNotification) {
            $existingNotification->delete();
            return;
        }

        // Si el stock está por debajo de la alerta y no hay notificación, crearla
        if ($stock->quantity_alert > 0 && 
            $stock->quantity <= $stock->quantity_alert && 
            !$existingNotification) {
            
            $data = [
                'message' => "El material {$stock->article->name} se encuentra por debajo del nivel de stock",
                'article_id' => $stock->id_article,
                'stockcenter_id' => $stock->id_stockcenter,
                'current_quantity' => $stock->quantity,
                'alert_quantity' => $stock->quantity_alert,
            ];
            
            $user->notify(new Notificationalert($data));
        }
    }

    public function getAvailableStockcenters(): Collection
    {
        return Stockcenter::whereNotIn('type', ['P', 'C'])->get();
    }

    public function increaseStock(Refer $refer, Collection $movements): void
    {
        $this->adjustStock($refer, $movements, true);
    }

    public function decreaseStock(Refer $refer, Collection $movements): void
    {
        $this->adjustStock($refer, $movements, false);
    }
}