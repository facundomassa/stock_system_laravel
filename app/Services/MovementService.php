<?php

namespace App\Services;

use App\Models\Movement;
use App\Models\Refer;
use App\Models\Stock;
use App\DTOs\MovementData;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MovementService
{
    public function paginateMovements(int $perPage = 20): LengthAwarePaginator
    {
        return Movement::with('article')->orderBy('id', 'desc')->paginate($perPage);
    }

    public function getMovementsByRefer(int $referId): Collection
    {
        return Movement::where('id_refer', $referId)->get();
    }

    public function processBatchMovements(array $movementsData, int $referId): array
    {
        $results = [
            'deleted' => 0,
            'deleted_total' => 0,
            'updated' => 0,
            'updated_total' => 0,
            'created' => 0,
            'created_total' => 0,
            'errors' => []
        ];

        foreach ($movementsData as $key => $data) {
            $movementData = new MovementData(
                id: $data['id'] ?? null,
                id_refer: $referId,
                id_article: $data['id_article'],
                quantity: $data['quantity'],
                delete: $data['delete'] ?? false
            );

            try {
                if ($movementData->delete && $movementData->id) {
                    $results['deleted_total']++;
                    if ($this->deleteMovement($movementData->id)) {
                        $results['deleted']++;
                    }
                } elseif ($movementData->id) {
                    $results['updated_total']++;
                    if ($this->updateMovement($movementData)) {
                        $results['updated']++;
                    }
                } else {
                    $results['created_total']++;
                    if ($this->createMovement($movementData)) {
                        $results['created']++;
                    }
                }
            } catch (\Exception $e) {
                $results['errors'][] = "Error en movimiento {$key}: " . $e->getMessage();
            }
        }

        return $results;
    }

    public function createMovement(MovementData $data): bool
    {
        return (bool) Movement::create([
            'id_refer' => $data->id_refer,
            'id_article' => $data->id_article,
            'quantity' => $data->quantity
        ]);
    }

    public function updateMovement(MovementData $data): bool
    {
        $movement = Movement::findOrFail($data->id);
        return $movement->update([
            'id_article' => $data->id_article,
            'quantity' => $data->quantity
        ]);
    }

    public function deleteMovement(int $id): bool
    {
        $movement = Movement::findOrFail($id);
        return $movement->delete();
    }

    public function getMovement(int $id): Movement
    {
        return Movement::findOrFail($id);
    }

    public function getTransitMovements(int $perPage = 10): LengthAwarePaginator
    {
        $movements = Movement::whereHas('refer', function ($query) {
            $query->where('status', 'E');
        })->get();

        $grouped = $movements->groupBy('id_refer');

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        return new LengthAwarePaginator(
            $grouped->forPage($currentPage, $perPage),
            $grouped->count(),
            $perPage,
            $currentPage,
            ['path' => LengthAwarePaginator::resolveCurrentPath()]
        );
    }

    public function enrichMovementsWithArticleInfo(Collection $movements): Collection
    {
        return $movements->each(function ($movement) {
            $movement->load('article');
        });
    }

    public function enrichMovementsWithStockInfo(Collection $movements, Refer $refer): Collection
    {
        $articleIds = $movements->pluck('id_article');
        
        $stocks = Stock::where('id_stockcenter', $refer->origen_id_stockcenter)
            ->whereIn('id_article', $articleIds)
            ->get()
            ->keyBy('id_article');

        return $movements->each(function ($movement) use ($stocks) {
            $movement->stock = $stocks->get($movement->id_article);
        });
    }
}