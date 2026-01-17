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
    // Métodos adicionales para el DashboardService
    public function getStockRotationData(): array
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);
        
        // Total de movimientos en los últimos 30 días
        $totalMovements = Movement::where('created_at', '>=', $thirtyDaysAgo)
            ->sum('quantity');
        
        // Stock promedio (simplificado: stock actual total)
        $averageStock = Stock::sum('quantity');
        
        // Calcular tasa de rotación
        $rotationRate = $averageStock > 0 ? round($totalMovements / $averageStock, 2) : 0;
        
        // Calcular tendencia (comparar con el mes anterior)
        $sixtyDaysAgo = Carbon::now()->subDays(60);
        $previousMonthMovements = Movement::whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])
            ->sum('quantity');
        
        // Stock del mes anterior (aproximado)
        $previousMonthStock = $averageStock; // Simplificación
        
        $previousRotationRate = $previousMonthStock > 0 ? 
            round($previousMonthMovements / $previousMonthStock, 2) : 0;
        
        $trend = $previousRotationRate > 0 ? 
            round((($rotationRate - $previousRotationRate) / $previousRotationRate) * 100, 1) : 0;
        
        return [
            'rotation_rate' => $rotationRate,
            'total_movements' => $totalMovements,
            'average_stock' => $averageStock,
            'trend' => $trend,
            'trend_direction' => $trend > 0 ? 'up' : ($trend < 0 ? 'down' : 'stable'),
            'previous_rate' => $previousRotationRate,
        ];
    }

    private function getStockSnapshot(Carbon $startDate, Carbon $endDate): float
    {
        // Obtener el stock promedio en un periodo (simplificado)
        // En un sistema real, podrías tener snapshots diarios de stock
        return Stock::avg('quantity') ?? 0;
    }

    public function getDeadStock(int $months = 6): Collection
    {
        $dateThreshold = Carbon::now()->subMonths($months);
        
        // Artículos que han tenido movimientos recientes (en cualquier centro)
        $activeArticleIds = Movement::where('created_at', '>=', $dateThreshold)
            ->distinct()
            ->pluck('id_article');
        
        // Si hay artículos activos, excluirlos
        $query = Stock::with(['article', 'stockCenter'])
            ->where('quantity', '>', 0);
        
        if ($activeArticleIds->isNotEmpty()) {
            $query->whereNotIn('id_article', $activeArticleIds);
        }
        
        return $query->orderBy('quantity', 'desc')
            ->limit(10)
            ->get();
    }

    public function getDispatchEfficiency(): array
    {
        // Obtener remitos finalizados con fechas válidas
        $refers = Refer::where('status', 'F')
            ->whereNotNull('date_up')
            ->whereNotNull('date_ended')
            ->where('date_up', '!=', '0000-00-00 00:00:00')
            ->where('date_ended', '!=', '0000-00-00 00:00:00')
            ->get();
        
        if ($refers->isEmpty()) {
            return [
                'avg_dispatch_time' => 0,
                'total_refers' => 0,
                'fastest_dispatch' => 0,
                'slowest_dispatch' => 0,
                'trend' => 0,
            ];
        }
        
        // Calcular tiempos de despacho en horas
        $dispatchTimes = $refers->map(function ($refer) {
            try {
                $start = Carbon::parse($refer->date_up);
                $end = Carbon::parse($refer->date_ended);
                return $end->diffInHours($start);
            } catch (\Exception $e) {
                return 0;
            }
        })->filter(fn($time) => $time > 0);
        
        if ($dispatchTimes->isEmpty()) {
            return [
                'avg_dispatch_time' => 0,
                'total_refers' => 0,
                'fastest_dispatch' => 0,
                'slowest_dispatch' => 0,
                'trend' => 0,
            ];
        }
        
        // Comparar con el mes anterior
        $lastMonthRefers = Refer::where('status', 'F')
            ->whereNotNull('date_up')
            ->whereNotNull('date_ended')
            ->whereBetween('date_ended', [
                Carbon::now()->subDays(60)->format('Y-m-d'),
                Carbon::now()->subDays(30)->format('Y-m-d'),
            ])
            ->get();
        
        $lastMonthAvg = $lastMonthRefers->isEmpty() ? 0 : 
            $lastMonthRefers->map(function ($refer) {
                try {
                    $start = Carbon::parse($refer->date_up);
                    $end = Carbon::parse($refer->date_ended);
                    return $end->diffInHours($start);
                } catch (\Exception $e) {
                    return 0;
                }
            })->filter(fn($time) => $time > 0)->avg();
        
        $currentAvg = $dispatchTimes->avg();
        $trend = $lastMonthAvg > 0 ? 
            (($lastMonthAvg - $currentAvg) / $lastMonthAvg) * 100 : 0;
        
        return [
            'avg_dispatch_time' => round($currentAvg, 1),
            'total_refers' => $refers->count(),
            'fastest_dispatch' => $dispatchTimes->min(),
            'slowest_dispatch' => $dispatchTimes->max(),
            'trend' => round($trend, 1),
            'trend_direction' => $trend > 0 ? 'improved' : ($trend < 0 ? 'worsened' : 'stable'),
        ];
    }

    public function getSystemRecommendations(): array
    {
        $recommendations = [];
        
        // 1. Verificar stock negativo
        $negativeStocks = $this->getNegativeStocks();
        if ($negativeStocks->isNotEmpty()) {
            $recommendations[] = [
                'type' => 'danger',
                'icon' => 'exclamation-triangle',
                'title' => 'Stock Negativo Detectado',
                'message' => 'Hay ' . $negativeStocks->count() . ' artículo(s) con stock negativo.',
                'action' => 'Ver Detalles',
                'action_url' => route('stock.index') . '?filter=negative',
                'priority' => 1,
            ];
        }
        
        // 2. Verificar stock muerto
        $deadStock = $this->getDeadStock(6);
        if ($deadStock->isNotEmpty()) {
            $totalDeadQuantity = $deadStock->sum('quantity');
            $recommendations[] = [
                'type' => 'warning',
                'icon' => 'skull-crossbones',
                'title' => 'Stock Sin Movimiento',
                'message' => count($deadStock) . ' artículos no han tenido movimiento en 6 meses.',
                'action' => 'Revisar',
                'action_url' => route('stock.index') . '?filter=dead',
                'priority' => 2,
            ];
        }
        
        // 3. Verificar alertas de stock bajo
        $lowStockAlerts = Stock::where('quantity_alert', '>', 0)
            ->whereRaw('quantity <= quantity_alert')
            ->count();
        
        if ($lowStockAlerts > 0) {
            $recommendations[] = [
                'type' => 'info',
                'icon' => 'bell',
                'title' => 'Alertas de Stock Bajo',
                'message' => $lowStockAlerts . ' artículo(s) han alcanzado su nivel de alerta.',
                'action' => 'Ver Alertas',
                'action_url' => route('stock.index') . '?filter=alerts',
                'priority' => 3,
            ];
        }
        
        // 4. Verificar remitos pendientes por mucho tiempo
        $oldPendingRefers = Refer::where('status', 'E')
            ->where('date_up', '<', Carbon::now()->subDays(7))
            ->count();
        
        if ($oldPendingRefers > 0) {
            $recommendations[] = [
                'type' => 'secondary',
                'icon' => 'clock',
                'title' => 'Remitos Pendientes',
                'message' => $oldPendingRefers . ' remito(s) pendientes por más de 7 días.',
                'action' => 'Ver Pendientes',
                'action_url' => route('refer.index') . '?status=E',
                'priority' => 4,
            ];
        }
        
        // 5. Verificar artículos sin stock en ningún centro
        $articlesWithoutStock = Article::whereDoesntHave('stocks', function ($query) {
            $query->where('quantity', '>', 0);
        })->count();
        
        if ($articlesWithoutStock > 0) {
            $recommendations[] = [
                'type' => 'light',
                'icon' => 'box-open',
                'title' => 'Artículos Sin Stock',
                'message' => $articlesWithoutStock . ' artículo(s) no tienen stock en ningún centro.',
                'action' => 'Ver Artículos',
                'action_url' => route('article.index') . '?filter=nostock',
                'priority' => 5,
            ];
        }
        
        // Ordenar por prioridad
        usort($recommendations, fn($a, $b) => $a['priority'] <=> $b['priority']);
        
        return $recommendations;
    }

    // Método para obtener todas las métricas del dashboard
    public function getDashboardMetrics(): array
    {
        return [
            'rotation' => $this->getStockRotationData(),
            'dead_stock' => $this->getDeadStock(),
            'dispatch_efficiency' => $this->getDispatchEfficiency(),
            'recommendations' => $this->getSystemRecommendations(),
            'summary' => $this->getDashboardSummary(),
            'negative_stocks' => $this->getNegativeStocks(),
        ];
    }
}