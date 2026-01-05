<?php

namespace App\Services;

use App\Models\Refer;
use App\Models\Movement;
use App\Models\Stockcenter;
use App\Models\User;
use App\Models\Article;
use App\Models\Stock;
use App\DTOs\ReferStatusData;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReferService
{
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    public function paginateRefers(
        ?string $originStockCenter,
        ?string $destinyStockCenter,
        ?string $status,
        Collection $allowedDestinations,
        int $perPage = 20
    ): LengthAwarePaginator {
        $query = Refer::query();

        if ($originStockCenter && $originStockCenter !== '*') {
            $query->where('origen_id_stockcenter', $originStockCenter);
        }

        if ($destinyStockCenter && $destinyStockCenter !== '*') {
            $query->where('destiny_id_stockcenter', $destinyStockCenter);
        }

        if ($status && $status !== '*') {
            $query->where('status', $status);
        }

        // Filtrar por destinos permitidos
        if ($allowedDestinations->isNotEmpty()) {
            $query->whereIn('destiny_id_stockcenter', $allowedDestinations);
        }

        return $query->with(['origin', 'destiny', 'user'])
            ->orderBy('id', 'desc')
            ->paginate($perPage);
    }

    public function getArticlesWithStockInfo(int $referId): Collection
    {
        $refer = $this->findRefer($referId);
        $articles = Article::all();
        
        $stockCenterId = $refer->origen_id_stockcenter;
        $articleIds = $articles->pluck('id');
        
        $stocks = Stock::where('id_stockcenter', $stockCenterId)
            ->whereIn('id_article', $articleIds)
            ->get()
            ->keyBy('id_article');
        
        return $articles->each(function ($article) use ($stocks) {
            $article->stock_quantity = $stocks->get($article->id)?->quantity;
            $article->unit_name = $article->unit_name;
        });
    }

    public function createRefer(array $data): Refer
    {

        return DB::transaction(function () use ($data) {
            $data['status'] = 'I'; // Estado inicial: Ingresado
            $data['date_up'] = $data['date_up'] ?? now();
            $data['id_user'] = auth()->id();
            return Refer::create($data);
        });
    }

    public function updateRefer(int $id, array $data): Refer
    {
        return DB::transaction(function () use ($id, $data) {
            $refer = Refer::findOrFail($id);
            
            // No permitir cambiar el estado desde aquí
            unset($data['status']);
            
            $refer->update($data);
            
            return $refer;
        });
    }

    public function findRefer(int $id): Refer
    {
        return Refer::with(['origin', 'destiny', 'user'])->findOrFail($id);
    }

    public function getReferWithMovements(int $id): array
    {
        $refer = $this->findRefer($id);
        $movements = Movement::where('id_refer', $id)
            ->with('article')
            ->paginate(10);

        return compact('refer', 'movements');
    }

    public function cancelRefer(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $refer = Refer::findOrFail($id);
            
            // Solo se puede cancelar si está ingresado o emitido
            if (!in_array($refer->status, ['I', 'E'])) {
                throw new \Exception('No se puede cancelar un remito finalizado.');
            }
            
            // Si está emitido, revertir los descuentos de stock
            if ($refer->status === 'E') {
                $movements = Movement::where('id_refer', $id)->get();
                
                // Revertir descuentos (aumentar stock en origen)
                if ($refer->origin->type !== 'P') {
                    $this->stockService->increaseStock($refer, $movements);
                }
            }
            
            return $refer->update(['status' => 'C']);
        });
    }

    public function emitRefer(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $refer = Refer::findOrFail($id);
            
            // Solo se puede emitir si está ingresado
            if ($refer->status !== 'I') {
                throw new \Exception('Solo se pueden emitir remitos ingresados.');
            }
            
            // Verificar que tenga movimientos
            $movements = Movement::where('id_refer', $id)->get();
            if ($movements->isEmpty()) {
                throw new \Exception('No se puede emitir un remito sin movimientos.');
            }
            
            // Descontar stock del origen si no es proveedor
            if ($refer->origin->type !== 'P') {
                $this->stockService->decreaseStock($refer, $movements);
            }
            
            return $refer->update(['status' => 'E']);
        });
    }

    public function finalizeRefer(int $id, ?string $dateEnded = null): bool
    {
        return DB::transaction(function () use ($id, $dateEnded) {
            $refer = Refer::findOrFail($id);
            
            // Solo se puede finalizar si está emitido
            if ($refer->status !== 'E') {
                throw new \Exception('Solo se pueden finalizar remitos emitidos.');
            }
            
            // Aumentar stock en el destino si no es cliente
            if ($refer->destiny->type !== 'C') {
                $movements = Movement::where('id_refer', $id)->get();
                $this->stockService->increaseStock($refer, $movements);
            }
            
            $updateData = ['status' => 'F'];
            
            if ($dateEnded) {
                $updateData['date_ended'] = $dateEnded;
            } else {
                $updateData['date_ended'] = now();
            }
            
            return $refer->update($updateData);
        });
    }

    public function getAvailableOrigins(Collection $allowedOperations): Collection
    {
        if ($allowedOperations->isEmpty()) {
            return collect();
        }

        return Stockcenter::query()
            ->whereHas('operation', function ($query) use ($allowedOperations) {
                $query->whereIn('operations.id', $allowedOperations);
            })
            ->get();
    }

    public function getAllStockcenters(): Collection
    {
        return Stockcenter::all();
    }

    public function getReferForPdf(int $id): array
    {
        $refer = $this->findRefer($id);
        $movements = Movement::where('id_refer', $id)
            ->with('article')
            ->get();

        return compact('refer', 'movements');
    }

    public function validateStockcenterRelations(array $data): array
    {
        if (!Stockcenter::where('id', $data['origen_id_stockcenter'])->exists()) {
            $data['origen_id_stockcenter'] = null;
        }
        
        if (!Stockcenter::where('id', $data['destiny_id_stockcenter'])->exists()) {
            $data['destiny_id_stockcenter'] = null;
        }
        
        if (isset($data['id_user']) && !User::where('id', $data['id_user'])->exists()) {
            $data['id_user'] = null;
        }
        
        return $data;
    }
}