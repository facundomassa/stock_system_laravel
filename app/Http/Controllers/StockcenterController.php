<?php

namespace App\Http\Controllers;

use App\Models\Stockcenter;
use App\Models\Enterprise;
use App\Models\Direction;
use App\Models\Person;
use Illuminate\Http\Request;

class StockcenterController extends Controller
{
    protected static $tittle = 'Centros de Stock';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['stockcenters'] = Stockcenter::paginate(20);

        foreach ($data['stockcenters'] as $key => $value) {

            $data['stockcenters'][$key]->id_enterprise = $value->Enterprise->name;

            $direction = $value->Direction;
            $data['stockcenters'][$key]->id_direction = $direction ? $direction->street . " " . $direction->number : "";

            $person = $value->Person;
            $data['stockcenters'][$key]->id_person = $person ? $person->name . " " . $person->surname : " ";

            $data['stockcenters'][$key]->TypeOf();
        }

        return view('stockcenter/index')->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data['enterprises'] = Enterprise::get();
        $data['directions'] = Direction::get();
        $data['persons'] = Person::get();

        return view('stockcenter/create')->with('tittle', static::$tittle)->with($data);
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
        $request = Stockcenter::ValidateIDRel($request);

        $data = [
            'id_enterprise' => 'required|integer|digits_between:0,10',
            'name' => 'required|string|max:60',
            'type' => 'required|string|max:1',
            'id_direction' => 'required|integer|digits_between:0,10',
            'id_person' => 'nullable|integer|digits_between:0,10',
        ];
        $message = [
            'required' => 'El :attribute es requerido',
            'max' => 'El :attribute no puedo tener mas de :max caracteres'
        ];

        $this->validate($request, $data, $message);

        $dataStockcenter = request()->except('_token');

        Stockcenter::insert($dataStockcenter);

        return redirect('stockcenter')->with('mensaje', 'Articulo agregado con exito')->with('tittle', static::$tittle);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Stockcenter  $stockcenter
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //

        $stockcenter = Stockcenter::findOrFail($id);

        $stockcenter->id_enterprise = $stockcenter->Enterprise->name;

        $direction = $stockcenter->Direction;
        $stockcenter->id_direction = $direction ? $direction->street . " " . $direction->number : "";

        $person = $stockcenter->Person;
        $stockcenter->id_person = $person ? $person->name . " " . $person->surname : "";

        $stockcenter->TypeOf();

        return view('stockcenter.show', compact('stockcenter'))->with('tittle', static::$tittle);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Stockcenter  $stockcenter
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data['enterprises'] = Enterprise::get();
        $data['directions'] = Direction::get();
        $data['persons'] = Person::get();

        $stockcenter = Stockcenter::findOrFail($id);

        return view('stockcenter.edit', compact('stockcenter'))->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Stockcenter  $stockcenter
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $request = Stockcenter::ValidateIDRel($request);

        $data = [
            'id_enterprise' => 'required|integer|digits_between:0,10',
            'name' => 'required|string|max:60',
            'type' => 'required|string|max:1',
            'id_direction' => 'required|integer|digits_between:0,10',
            'id_person' => 'nullable|integer|digits_between:0,10',
        ];
        $message = [
            'required' => 'El :attribute es requerido',
            'max' => 'El :attribute no puedo tener mas de :max caracteres'
        ];

        $this->validate($request, $data, $message);

        $dataStockcenter = request()->except(['_token', '_method']);

        Stockcenter::where('id', '=', $id)->update($dataStockcenter);
        return redirect('stockcenter')->with('mensaje', 'Centro de Stock editado con exito')->with('tittle', static::$tittle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Stockcenter  $stockcenter
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $stockcenter = Stockcenter::findOrFail($id);

        Stockcenter::destroy($id);

        return redirect('stockcenter')->with('mensaje', 'Centro de Stock eliminado')->with('tittle', static::$tittle);
    }
}
