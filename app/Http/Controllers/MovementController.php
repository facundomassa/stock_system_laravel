<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Refer;
use App\Models\Article;
use App\Models\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class MovementController extends Controller
{
    protected static $tittle = 'Movimientos';

    private static $rules = [
        'id_refer' => 'required|digits_between:0,10|integer',
        'id_article' => 'required|digits_between:0,10|integer',
        'quantity' => 'required|digits_between:0,11|integer',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['movements'] = Movement::orderBy('id', 'desc')->paginate(20);

        foreach ($data['movements'] as $key => $value) {
            $data['movements'][$key]->id_article = $value->Article->name;
        }

        return view('movement/index')->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        //
        $refer = Refer::findOrFail($id);

        $data['movements'] = Movement::where('id_refer', '=', $id)->get();
        $data['id_refer'] = $id;

        foreach ($data['movements'] as $key => $value) {
            $data['movements'][$key]->stock = Stock::where('id_stockcenter', $refer->origen_id_stockcenter)
            ->where('id_article', $data['movements'][$key]->id_article)->first();
            $data['movements'][$key]->id_article = $value->Article;
            
        }

        $data['articles'] = Article::get();
        foreach ($data['articles'] as $key => $value) {
            $data['articles'][$key]->StockQuantity($refer);
            // dd($data['articles'][$key]);
        }
        
        return view('movement/create')->with('tittle', static::$tittle)->with($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $deleteTotal = $delete = $createTotal = $create = $updateTotal = $update = 0;
        $id_refer = $request->id_refer;
        $data = request()->except(['_token', 'id_refer']);

        foreach ($data as $key => $value) {
            $data[$key]['id_refer'] = $id_refer;

            if (isset($value['delete']) && $value['delete'] == 'on') {
                $deleteTotal++;

                if ($value['id'] != null) {
                    MovementController::destroy($value['id']) ? $delete++ : $delete;
                }
            } elseif (isset($value['id'])) {
                $updateTotal++;
                
                $validator = $this->validates($data[$key]);
                if ($validator->fails()) {
                    continue;
                } else {
                    Movement::where('id', '=', $value['id'])->update($data[$key]);
                    $update++;
                }
            } else {
                $createTotal++;
                
                $validator = $this->validates($data[$key]);
                if ($validator->fails()) {
                    continue;
                } else {
                    Movement::insert($data[$key]);
                    $create++;
                }
                
            }
        }
        return redirect('refer/'.$id_refer)->with('mensaje', 
        "Se eliminaron " . $delete . " de un total de " . $deleteTotal . "<br>" .
        "Se actualizo " . $update . " de un total de " . $updateTotal . "<br>" .
        "Se crearon " . $create . " de un total de " . $createTotal . "<br>")->with('tittle', static::$tittle);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Movement  $movement
     * @return \Illuminate\Http\Response
     */
    public function show($id_refer)
    {
        //
        $data['refer'] = Refer::findOrFail($id_refer);

        $data['movements'] = Movement::where('id_refer', '=', $id_refer)->get();
        foreach ($data['movements'] as $key => $value) {
            $data['movements'][$key]->id_article = $value->Article;
        }

        return view('movement.show')->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Movement  $movement
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $movement = Movement::findOrFail($id);

        Movement::destroy($id);

        return true;
    }

    public function validates($data)
    {
        return Validator::make($data, static::$rules);
    }

    
}
