<?php

namespace App\Http\Controllers;

use App\Models\Stock;
use App\Models\Stockcenter;
use Illuminate\Http\Request;

class StockController extends Controller
{
    protected static $tittle = 'Stock';

    private static $rules = array(
        'id_stockcenter' => 'required|digits_between:0,10|integer',
        'id_article' => 'required|digits_between:0,10|integer',
        'quantity' => 'required|digits_between:0,11|integer',
    );
    private static $message = array(
        'required' => 'El :attribute es requerido',
        'digits_between' => 'El :attribute debe tener entre 0 y 11 digitos'
    );
    /**
     * Display a listing of the resource.
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
        // $options[] = request()->get('options');
        // dd($options);
        $data['stocks'] = Stock::StockCenters($options['stockselect'])
            ->Type($options['type'])
            ->Articles($options['articlename'])
            ->Code($options['code'])
            ->orderBy('id_stockcenter', 'asc')
            ->leftJoin('articles', 'stocks.id_article', '=', 'articles.id')
            ->orderBy('articles.name', 'asc')
            ->paginate(20);
        // dd( $article);
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
    public function show(Stock $stock)
    {
        //
    }

    public static function discount($refer, $movements)
    {
        //
        foreach ($movements as $movement) {
            $stockQuery = Stock::where("id_stockcenter", $refer['origen_id_stockcenter'])->where('id_article', $movement->id_article);
            if ($stockQuery->exists()) {
                $dataStock = $stockQuery->first()->getAttributes();
                $dataStock['quantity'] -= $movement->quantity;
                Stock::find($dataStock['id'])->update($dataStock);
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
                Stock::find($dataStock['id'])->update($dataStock);
            } else {
                $dataStock['id_stockcenter'] = $refer['destiny_id_stockcenter'];
                $dataStock['id_article'] = $movement->id_article;
                $dataStock['quantity'] = $movement->quantity;
                Stock::create($dataStock);
            }
        }
    }
}
