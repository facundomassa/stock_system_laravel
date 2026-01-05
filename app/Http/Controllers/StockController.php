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
        $stocks = $this->stockService->filterStocks(
            stockcenterId: request('stockselect'),
            type: request('type'),
            articleName: request('articlename'),
            code: request('code')
        );

        $filters = [
            'stockselect' => request('stockselect'),
            'type' => request('type'),
            'articlename' => request('articlename'),
            'code' => request('code'),
        ];

        $pdf = PDF::loadView('stock.pdf', compact('stocks', 'filters'))
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->stream('stock_' . date('Ymd_His') . '.pdf');
    }

    /**
     * Mostrar lista de stocks con paginación
     */
    public function index()
    {
        $stockcenters = $this->stockService->getAvailableStockcenters();
        
        $stocks = $this->stockService->paginateStocks(
            stockcenterId: request('stockselect'),
            type: request('type'),
            articleName: request('articlename'),
            code: request('code'),
            perPage: 20
        );

        $filters = [
            'stockselect' => request('stockselect'),
            'type' => request('type'),
            'articlename' => request('articlename'),
            'code' => request('code'),
        ];

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