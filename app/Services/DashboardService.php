<?php

namespace App\Services;

use App\Models\Stock;
use App\Models\Refer;
use App\Models\Article;
use App\Models\Movement;
use App\Models\Stockcenter;
use App\Models\User;
use App\Notifications\Notificationalert;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardService
{
    public function getNegativeStocks(): Collection
    {
        return Stock::where('quantity', '<', 0)
            ->with(['article', 'stockCenter'])
            ->get();
    }

    public function getUserNotifications(User $user): Collection
    {
        return $user->notifications;
    }

    public function deleteNotification(User $user, string $notificationId): bool
    {
        $notification = $user->notifications()->find($notificationId);
        
        if ($notification) {
            $notification->delete();
            return true;
        }
        
        return false;
    }

    public function getStockCenters(): Collection
    {
        return Stockcenter::all();
    }

    public function getRecentMovements(array $filters = []): array
    {
        // Fechas por defecto (último mes)
        $today = Carbon::today();
        $oneMonthAgo = Carbon::today()->subMonth();
        
        $startDate = $filters['date_start'] ?? $oneMonthAgo->format('Y-m-d');
        $endDate = $filters['date_end'] ?? $today->format('Y-m-d');
        
        $query = Refer::query();
        
        // Aplicar filtros de centros de stock
        if (!empty($filters['stockselectorigen'])) {
            $query->where('origen_id_stockcenter', $filters['stockselectorigen']);
        }
        
        if (!empty($filters['stockselectdestiny'])) {
            $query->where('destiny_id_stockcenter', $filters['stockselectdestiny']);
        }
        
        // Filtrar por fecha
        $query->whereBetween('date_up', [$startDate, $endDate]);
        
        // Obtener IDs de referencias
        $referIds = $query->pluck('id');
        
        if ($referIds->isEmpty()) {
            return [];
        }
        
        // Agrupar movimientos por artículo
        $movements = Movement::whereIn('id_refer', $referIds)
            ->select('id_article', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('id_article')
            ->get();
        
        // Obtener nombres de artículos
        $articleIds = $movements->pluck('id_article');
        $articles = Article::whereIn('id', $articleIds)
            ->pluck('name', 'id');
        
        // Formatear resultado
        $result = [];
        foreach ($movements as $movement) {
            if (isset($articles[$movement->id_article])) {
                $result[$articles[$movement->id_article]] = $movement->total_quantity;
            }
        }
        
        return $result;
    }

    public function getTotalStockByArticle(): array
    {
        $stocks = Stock::select('id_article', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('id_article')
            ->get();
        
        $articleIds = $stocks->pluck('id_article');
        $articles = Article::whereIn('id', $articleIds)
            ->pluck('name', 'id');
        
        $result = [];
        foreach ($stocks as $stock) {
            if (isset($articles[$stock->id_article])) {
                $result[$articles[$stock->id_article]] = $stock->total_quantity;
            }
        }
        
        return $result;
    }

    public function getDashboardSummary(): array
    {
        $today = Carbon::today();
        $oneMonthAgo = Carbon::today()->subMonth();
        
        return [
            'total_articles' => Article::count(),
            'total_stock_centers' => Stockcenter::count(),
            'total_stock_value' => Stock::sum('quantity'),
            'pending_refers' => Refer::where('status', 'E')->count(),
            'completed_refers_month' => Refer::where('status', 'F')
                ->whereBetween('date_ended', [$oneMonthAgo, $today])
                ->count(),
            'alert_stocks' => Stock::where('quantity_alert', '>', 0)
                ->whereRaw('quantity <= quantity_alert')
                ->count(),
            'negative_stocks' => Stock::where('quantity', '<', 0)->count(),
        ];
    }
}