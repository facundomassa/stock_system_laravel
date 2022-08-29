<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DirectionController extends Controller
{
    protected static $tittle = 'Direcciones';

    private static $data = [
        'country' => 'required|string|max:60',
        'state' => 'required|string|max:60',
        'city' => 'required|string|max:60',
        'locality' => 'required|string|max:100',
        'street' => 'required|string|max:100',
        'number' => 'required|digits_between:1,11|integer',
        'department' => 'nullable|digits_between:0,6|integer',
        'house' => 'nullable|digits_between:0,6|integer',
        'floor' => 'nullable|digits_between:0,4|integer',
        'cp' => 'required|digits_between:0,11|integer',
    ];
    private static $message = [
        'required' => 'El :attribute es requerido',
        'max' => 'El :attribute no puedo tener mas de :max caracteres'
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['directions'] = Direction::paginate(20);
        return view('direction/index')->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        $countrys = Http::get(url('api/country'));
        return view('direction/create')->with('tittle', static::$tittle)->with('countrys',$countrys->json());
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
        $this->validate($request, static::$data, static::$message);
        

        $dataDirection = request()->except('_token');
        
        Direction::create($dataDirection);
        return redirect('direction')->with('mensaje', 'Direccion agregada con exito')->with('tittle', static::$tittle);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Direction  $direction
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $direction = Direction::findOrFail($id);
        return view('direction.show', compact('direction'))->with('tittle', static::$tittle);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Direction  $direction
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $countrys = Http::get(url('api/country'));
        $direction = Direction::findOrFail($id);
        return view('direction.edit', compact('direction'))->with('tittle', static::$tittle)->with('countrys',$countrys->json());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Direction  $direction
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, static::$data, static::$message);

        $dataDirection = request()->except(['_token', '_method']);

        Direction::find($id)->update($dataDirection);
        return redirect('direction')->with('mensaje', 'Direccion editada con exito')->with('tittle', static::$tittle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Direction  $direction
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $direction = Direction::findOrFail($id);

        Direction::destroy($id);

        return redirect('direction')->with('mensaje', 'Direccion eliminada')->with('tittle', static::$tittle);
    }
}
