<?php

namespace App\Services;

use App\Models\Refer;
use App\Models\Movement;
use App\Models\Stock;
use App\Models\Article;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getStockReport(array $filters = []): Collection
    {
        $query = Stock::query();
        
        if (!empty($filters['stockselect']) && $filters['stockselect'] !== '*') {
            $query->where('id_stockcenter', $filters['stockselect']);
        }
        
        if (!empty($filters['type'])) {
            $articleIds = Article::where('type', 'LIKE', "%{$filters['type']}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }
        
        if (!empty($filters['articlename'])) {
            $articleIds = Article::where('name', 'LIKE', "%{$filters['articlename']}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }
        
        if (!empty($filters['code'])) {
            $articleIds = Article::where('code', 'LIKE', "%{$filters['code']}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }
        
        return $query->with(['article', 'stockCenter'])
            ->orderBy('id_stockcenter')
            ->orderBy('id_article')
            ->get();
    }

    public function getMovementReport(array $filters = []): Collection
    {
        $query = Movement::query();
        
        if (!empty($filters['date_start']) && !empty($filters['date_end'])) {
            $referIds = Refer::whereBetween('date_ended', [
                $filters['date_start'],
                $filters['date_end']
            ])->pluck('id');
            
            if ($referIds->isNotEmpty()) {
                $query->whereIn('id_refer', $referIds);
            } else {
                return collect(); // No hay referencias en el rango de fechas
            }
        }
        
        return $query->with(['article', 'refer.origin', 'refer.destiny', 'refer.user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getConsumptionReport(array $filters = []): Collection
    {
        $query = Refer::query();
        
        if (!empty($filters['date_start']) && !empty($filters['date_end'])) {
            $query->whereBetween('date_ended', [
                $filters['date_start'],
                $filters['date_end']
            ]);
        }
        
        if (!empty($filters['check_origin']) && $filters['check_origin'] !== '*') {
            $query->whereIn('origen_id_stockcenter', (array)$filters['check_origin']);
        }
        
        if (!empty($filters['check_destiny']) && $filters['check_destiny'] !== '*') {
            $query->whereIn('destiny_id_stockcenter', (array)$filters['check_destiny']);
        }
        
        $referIds = $query->pluck('id');
        
        if ($referIds->isEmpty()) {
            return collect();
        }
        
        return Movement::whereIn('id_refer', $referIds)
            ->select('id_article', DB::raw('SUM(quantity) as total_quantity'))
            ->groupBy('id_article')
            ->with('article')
            ->get();
    }

    public function getReportSummary(array $filters = [], string $reportType): array
    {
        $summary = [
            'report_type' => $reportType,
            'filters_applied' => $filters,
            'generated_at' => now()->format('Y-m-d H:i:s'),
            'generated_by' => auth()->user()->name ?? 'System',
        ];
        
        switch ($reportType) {
            case 'stock':
                $data = $this->getStockReport($filters);
                $summary['total_records'] = $data->count();
                $summary['total_quantity'] = $data->sum('quantity');
                $summary['alerts_count'] = $data->filter(fn($stock) => $stock->warning)->count();
                break;
                
            case 'movement':
                $data = $this->getMovementReport($filters);
                $summary['total_records'] = $data->count();
                $summary['total_quantity'] = $data->sum('quantity');
                $summary['unique_articles'] = $data->unique('id_article')->count();
                break;
                
            case 'consumption':
                $data = $this->getConsumptionReport($filters);
                $summary['total_records'] = $data->count();
                $summary['total_quantity'] = $data->sum('total_quantity');
                $summary['unique_articles'] = $data->count();
                break;
        }
        
        return $summary;
    }
}