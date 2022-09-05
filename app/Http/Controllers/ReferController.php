<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Refer;
use Illuminate\Http\Request;
use App\Models\Stockcenter;

class ReferController extends Controller
{
    protected static $tittle = 'Remito';

    private static $rules = [
        'origen_id_stockcenter' => 'required|integer|digits_between:0,10',
        'destiny_id_stockcenter' => 'required|integer|digits_between:0,10',
        'status' => 'required|string|max:1',
        'date_ended' => 'nullable|date_format:Y-m-d H:i|after:-1 days',
        'id_user' => 'required|integer|digits_between:0,10',
    ];
    private static $message = [
        'required' => 'El :attribute es requerido',
        'date_ended.required' => 'Falta fecha finalizado',
        'max' => 'El :attribute no puedo tener mas de :max caracteres',
        'after' => 'La fecha no puede ser menor al ingreso de hoy'
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['refers'] = Refer::paginate(20);
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
        $request['id_user'] = auth()->user()->id;
        $request['status'] = 'I';
        
        if(isset($request['date_ended']) && $request['date_ended'] != null){
            $request['date_ended'] = str_replace("T", " ", $request['date_ended']);
        }

        $request = Refer::ValidateIDRel($request);

        $this->validate($request, static::$rules, static::$message);

        $dataRefer = request()->except('_token');
        
        Refer::create($dataRefer);
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
        $data['movements'] = Movement::where('id_refer', $id)->paginate(10);

        $refer = Refer::findOrFail($id);
        return view('refer.show', compact('refer'))->with($data)->with('tittle', static::$tittle);
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
        return view('refer.edit', compact('refer'))->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Refer  $refer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $refer = Refer::find($id);
        $request['id_user'] = $refer->id_user;
        $request['status'] = $refer->status;

        if(isset($request['date_ended']) && $request['date_ended'] != null){
            $request['date_ended'] = str_replace("T", " ", $request['date_ended']);
        }

        $request = Refer::ValidateIDRel($request);
        
        $this->validate($request, static::$rules, static::$message);

        $dataRefer = request()->except(['_token', '_method']);
        
        $refer->update($dataRefer);
        return redirect('refer')->with('mensaje', 'Remito editado con exito')->with('tittle', static::$tittle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Refer  $refer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $refer = Refer::findOrFail($id);
        $refer->canceled();

        return redirect('refer')->with('mensaje', 'Remito cancelado')->with('tittle', static::$tittle);
    }

    public function emited($id)
    {
        //
        $refer = Refer::findOrFail($id);
        $refer->emited();

        return redirect('refer')->with('mensaje', 'Remito emitido')->with('tittle', static::$tittle);
    }

    public function finalized(Request $request, $id)
    {
        //
        $refer = Refer::findOrFail($id);
        $request->request->add($refer->getAttributes());
        
        $rules = static::$rules;
        $rules['date_ended'] = 'required|date_format:Y-m-d H:i:s';
        
        $this->validate($request, $rules, static::$message);
        
        if($refer->status == "I"){
            $refer->emited();
        }
        $refer->finalized();
        return redirect('refer')->with('mensaje', 'Remito Finalizado')->with('tittle', static::$tittle);
    }
}
