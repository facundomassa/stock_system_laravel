<?php

namespace App\Http\Controllers;

use App\Http\Requests\StockRequest;
use App\Services\StockService;
use App\Exports\StocksExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class StockController extends Controller
{
    protected string $title = 'Stock';
    protected StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;
    }

    /**
     * Generar PDF del stock
     */
    public function getPdf()
    {
        $filters = [
            'stockselect' => request('stockselect'),
            'type' => request('type'),
            'articlename' => request('articlename'),
            'code' => request('code'),
        ];
        
        $stocks = $this->stockService->filterStocks(
            stockcenterId: $filters['stockselect'],
            type: $filters['type'],
            articleName: $filters['articlename'],
            code: $filters['code']
        );
        
        // Estadísticas rápidas
        $stats = [
            'total' => $stocks->count(),
            'alerta' => $stocks->where('warning', true)->count(),
            'agotado' => $stocks->where('quantity', '<=', 0)->count(),
        ];
        
        $pdf = PDF::loadView('stock.pdf', [
            'stocks' => $stocks,
            'filters' => $filters,
            'stats' => $stats,
            'fecha' => now()->format('d/m/Y H:i')
        ])->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'margin_top' => 70,
            'margin_bottom' => 40,
            'margin_left' => 15,
            'margin_right' => 15
        ]);
        
        return $pdf->stream('stock_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Mostrar lista de stocks con paginación
     */
    public function index()
    {
        $stockcenters = $this->stockService->getAvailableStockcenters();
        
        // Manejar filtros predefinidos del dashboard
        $filters = [
            'stockselect' => request('stockselect'),
            'type' => request('type'),
            'articlename' => request('articlename'),
            'code' => request('code'),
        ];
        
        // Aplicar filtro predefinido si viene del dashboard
        if ($filterType = request('filter')) {
            switch ($filterType) {
                case 'negative':
                    $filters['negative'] = true;
                    break;
                case 'dead':
                    // Aquí podrías añadir lógica para filtrar stock muerto
                    // Necesitarías modificar el StockService para aceptar este filtro
                    break;
                case 'alerts':
                    $filters['alerts'] = true;
                    break;
            }
        }
        
        $stocks = $this->stockService->paginateStocks(
            stockcenterId: $filters['stockselect'],
            type: $filters['type'],
            articleName: $filters['articlename'],
            code: $filters['code'],
            perPage: 20
        );

        return view('stock.index', compact('stockcenters', 'stocks', 'filters'))
            ->with('title', $this->title);
    }

    /**
     * Mostrar detalles de un stock específico
     */
    public function show(int $id)
    {
        $stock = $this->stockService->findStock($id);
        $movements = $this->stockService->getStockMovements(
            articleId: $stock->id_article,
            stockcenterId: $stock->id_stockcenter,
            perPage: 20
        );

        return view('stock.show', compact('stock', 'movements'))
            ->with('title', $this->title);
    }

    /**
     * Actualizar alerta de stock
     */
    public function update(StockRequest $request, int $id)
    {
        $this->stockService->updateQuantityAlert($id, $request->quantity_alert);
        return redirect()->route('stock.index')
            ->with('success', 'Alerta de stock actualizada correctamente')
            ->with('title', $this->title);
    }

    /**
     * Exportar stock a Excel
     */
    public function getExcel()
    {
        $filters = [
            'stockselect' => request('stockselect'),
            'type' => request('type'),
            'articlename' => request('articlename'),
            'code' => request('code'),
        ];

        return Excel::download(new StocksExport($filters), 'stock_' . date('Ymd_His') . '.xlsx');
    }
}