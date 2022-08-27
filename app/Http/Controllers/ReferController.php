<?php

namespace App\Http\Controllers;

use App\Models\Refer;
use Illuminate\Http\Request;
use App\Models\Stockcenter;

class ReferController extends Controller
{
    protected static $tittle = 'Remito';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['refers'] = Refer::paginate(20);
        
        foreach ($data['refers'] as $key => $value) {
            $data['refers'][$key]->origen_id_stockcenter = $value->Origin->name;
            $data['refers'][$key]->destiny_id_stockcenter = $value->Destiny->name;

            $data['refers'][$key]->id_user = $value->User->name . " " . $value->User->surname;

            $data['refers'][$key]->status_n = $value->status;
            $data['refers'][$key]->StatusOf();
        }

        return view('refer/index')->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $data['stockcenters'] = Stockcenter::get();

        return view('refer/create')->with('tittle', static::$tittle)->with($data);
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
        $request = Refer::ValidateIDRel($request);
        $request->merge(['id_user' => auth()->id()]);

        if(isset($request->date_ended)){
            $request->merge(['date_ended' => str_replace("T", " ", $request->date_ended)]);
        }
        
        
        $data = [
            'origen_id_stockcenter' => 'required|integer|digits_between:0,10',
            'destiny_id_stockcenter' => 'required|integer|digits_between:0,10',
            'status' => 'required|string|max:1',
            'date_ended' => 'nullable|date_format:Y-m-d H:i|after:-1 days',
            'id_user' => 'required|integer|digits_between:0,10',
        ];
        $message = [
            'required' => 'El :attribute es requerido',
            'max' => 'El :attribute no puedo tener mas de :max caracteres',
            'after' => 'La fecha no puede ser menor al ingreso de hoy'
        ];

        $this->validate($request, $data, $message);

        $dataRefer = request()->except('_token');

        Refer::insert($dataRefer);
        
        return redirect('refer/' . Refer::latest('id')->first()->id)->with('mensaje', 'Remito creado con exito')->with('tittle', static::$tittle);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Refer  $refer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $refer = Refer::findOrFail($id);

        $refer->origen_id_stockcenter = $refer->Origin->name;
        $refer->destiny_id_stockcenter = $refer->Destiny->name;
        $refer->id_user = $refer->User->name . " " . $refer->User->surname;

        $refer->status_n = $refer->status;
        $refer->StatusOf();

        return view('refer.show', compact('refer'))->with('tittle', static::$tittle);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Refer  $refer
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $data['stockcenters'] = Stockcenter::get();

        $refer = Refer::findOrFail($id);
        $refer->StatusOf();
        
        return view('refer.edit', compact('refer'))->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Refer  $refer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Refer $refer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Refer  $refer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Refer $refer)
    {
        //
    }
}
