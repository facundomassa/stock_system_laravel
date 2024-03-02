<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Stockcenter;
use Illuminate\Http\Request;
use App\Exports\StocksExport;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class StockController extends Controller
{
    protected static $tittle = 'Stock';

    private static $rules = array(
        'id_stockcenter' => 'required|digits_between:0,10|integer',
        'id_article' => 'required|digits_between:0,10|integer',
        'quantity' => 'required|digits_between:0,11|integer',
        'limit' => 'nullable|digits_between:0,11|integer',
    );
    private static $message = array(
        'required' => 'El :attribute es requerido',
        'digits_between' => 'El :attribute debe tener entre 0 y 11 digitos'
    );

    public function getpdf(){

        $data['stockselect'] = request()->get('stockselect');
        $data['type'] = request()->get('type');
        $data['articlename'] = request()->get('articlename');
        $data['code'] = request()->get('code');

        $data['stocks'] = Stock::StockCenters($data['stockselect'])
            ->Type($data['type'])
            ->Articles($data['articlename'])
            ->Code($data['code'])
            ->orderBy('id_stockcenter', 'asc')
            ->leftJoin('articles', 'stocks.id_article', '=', 'articles.id')
            ->orderBy('articles.name', 'asc')
            ->get();
            
        $pdf = PDF::loadView('stock/pdf', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->stream('archivo-pdf.pdf');
    }

    /**
     * Display a listing of the resource.s
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['stockcenters'] = Stockcenter::whereNotIn('type', ['P', 'C'])->get();
        $options = [
            'stockselect' => request()->get('stockselect'),
            'type' => request()->get('type'),
            'articlename' => request()->get('articlename'),
            'code' => request()->get('code')
        ];

        $data['stocks'] = Stock::StockCenters($options['stockselect'])
            ->Type($options['type'])
            ->Articles($options['articlename'])
            ->Code($options['code'])
            ->orderBy('id_stockcenter', 'asc')
            // ->leftJoin('articles', 'stocks.id_article', '=', 'articles.id')
            // ->orderBy('articles.name', 'asc')
            ->paginate(20);
        return view('stock/index')
            ->with($data)
            ->with($options)
            ->with('tittle', static::$tittle);
    }

   

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stock  $stock
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $stock = Stock::findOrFail($id);
        return view('stock.show', compact('stock'))->with('tittle', static::$tittle);
    }

    public function update(Request $request, $id)
    {
        $stock = Stock::find($id);
        $stock->quantity_alert = request()->quantity_alert;
        $stock->update();
        return redirect('stock')->with('mensaje', 'Stock editado con exito')->with('tittle', static::$tittle);
    }

    public static function discount($refer, $movements)
    {
        //
        foreach ($movements as $movement) {
            $stockQuery = Stock::where("id_stockcenter", $refer['origen_id_stockcenter'])->where('id_article', $movement->id_article);
            if ($stockQuery->exists()) {
                $dataStock = $stockQuery->first()->getAttributes();
                $dataStock['quantity'] -= $movement->quantity;
                $stock = Stock::find($dataStock['id']);
                $stock->update($dataStock);
                $stock->updateAlert(false);
            } else {
                $dataStock['id_stockcenter'] = $refer['origen_id_stockcenter'];
                $dataStock['id_article'] = $movement->id_article;
                $dataStock['quantity'] = -$movement->quantity;
                Stock::create($dataStock);
            }
        }
    }

    public static function increase($refer, $movements)
    {
        //
        foreach ($movements as $movement) {
            $stockQuery = Stock::where("id_stockcenter", $refer['destiny_id_stockcenter'])->where('id_article', $movement->id_article);
            if ($stockQuery->exists()) {
                $dataStock = $stockQuery->first()->getAttributes();
                $dataStock['quantity'] += $movement->quantity;
                $stock = Stock::find($dataStock['id']);
                $stock->update($dataStock);
                $stock->updateAlert(true);
            } else {
                $dataStock['id_stockcenter'] = $refer['destiny_id_stockcenter'];
                $dataStock['id_article'] = $movement->id_article;
                $dataStock['quantity'] = $movement->quantity;
                Stock::create($dataStock);
            }
        }
    }

    // Para la función discount     adjustStock($refer, $movements, false);
    // Para la función increase     adjustStock($refer, $movements, true);
    public static function adjustStock($refer, $movements, $increase)
    {
        $stockcenterField = $increase ? 'destiny_id_stockcenter' : 'origen_id_stockcenter';
        $quantityMultiplier = $increase ? 1 : -1;

        foreach ($movements as $movement) {
            $stockQuery = Stock::where("id_stockcenter", $refer[$stockcenterField])->where('id_article', $movement->id_article);
            if ($stockQuery->exists()) {
                $dataStock = $stockQuery->first()->getAttributes();
                $dataStock['quantity'] += $quantityMultiplier * $movement->quantity;
                $stock = Stock::find($dataStock['id']);
                $stock->update($dataStock);
                $stock->updateAlert($increase);
            } else {
                $dataStock['id_stockcenter'] = $refer[$stockcenterField];
                $dataStock['id_article'] = $movement->id_article;
                $dataStock['quantity'] = $quantityMultiplier * $movement->quantity;
                Stock::create($dataStock);
            }
        }
    }
    

    public function getexcel(){
        
        return Excel::download(new StocksExport, 'stocks.xlsx');
    }
}
