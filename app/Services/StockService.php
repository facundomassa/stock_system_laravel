<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Refer;
use App\Models\Movement;
use App\Models\Article;
use App\Models\Stockcenter;
use App\Notifications\Notificationalert;
use App\Events\StockAlertUpdated;
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
        return Stock::stockCenters($stockcenterId)
            ->type($type)
            ->articles($articleName)
            ->code($code)
            ->withRelations()
            ->orderDefault()
            ->get();
    }

    public function paginateStocks(
        ?string $stockcenterId,
        ?string $type,
        ?string $articleName,
        ?string $code,
        int $perPage = 20
    ): LengthAwarePaginator {
        return Stock::stockCenters($stockcenterId)
            ->type($type)
            ->articles($articleName)
            ->code($code)
            ->withRelations()
            ->orderDefault()
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

    public function updateQuantityAlert(int $stockId, ?int $quantityAlert): Stock
    {
        $stock = Stock::findOrFail($stockId);
        $oldAlert = $stock->quantity_alert;
        
        $stock->update(['quantity_alert' => $quantityAlert]);

        // Disparar evento de actualización de alerta
        event(new StockAlertUpdated($stock, null, null, $oldAlert, $quantityAlert, 'alert'));

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

            $oldQuantity = $stock->quantity ?? 0;

            if ($increase) {
                $stock->quantity += $movement->quantity;
            } else {
                $stock->quantity -= $movement->quantity;
            }

            if ($stock->quantity < 0) {
                $stock->quantity = 0;
            }

            $stock->save();

            // Disparar evento de actualización de cantidad
            // dd($stock->quantity);
            event(new StockAlertUpdated($stock, $oldQuantity, $stock->quantity, null, null, 'quantity'));
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

    public function getStockSummary(array $filters = [])
    {
        $stocks = $this->filterStocks(
            stockcenterId: $filters['stockselect'] ?? null,
            type: $filters['type'] ?? null,
            articleName: $filters['articlename'] ?? null,
            code: $filters['code'] ?? null
        );
        
        return [
            'total_items' => $stocks->count(),
            'total_quantity' => $stocks->sum('quantity'),
            'total_value' => $stocks->sum(function($stock) {
                return $stock->quantity * ($stock->Article->cost ?? 0);
            }),
            'warning_count' => $stocks->where('warning', true)->count(),
            'zero_stock_count' => $stocks->where('quantity', '<=', 0)->count(),
        ];
    }
}