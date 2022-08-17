<?php

namespace App\Http\Controllers;

use App\Models\Enterprise;
use Illuminate\Http\Request;

class EnterpriseController extends Controller
{
    protected static $tittle = 'Operacion';
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $data['enterprises'] = Enterprise::paginate(20);
        return view('enterprise/index')->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('enterprise/create')->with('tittle', static::$tittle);
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
        $data = [
            'name' => 'required|string|max:255',
        ];
        $message = [
            'required' => 'El :attribute es requerido',
            'max' => 'El :attribute no puedo tener mas de :max caracteres'
        ];

        $this->validate($request, $data, $message);

        $dataEnterprise = request()->except('_token');

        Enterprise::insert($dataEnterprise);
        return redirect('enterprise')->with('mensaje', 'Operacion agregada con exito')->with('tittle', static::$tittle);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Enterprise  $enterprise
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $enterprise = Enterprise::findOrFail($id);
        return view('enterprise.show', compact('enterprise'))->with('tittle', static::$tittle);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Enterprise  $enterprise
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $enterprise = Enterprise::findOrFail($id);
        return view('enterprise.edit', compact('enterprise'))->with('tittle', static::$tittle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Enterprise  $enterprise
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $data = [
            'name' => 'required|string|max:60',
        ];
        $message = [
            'required' => 'El :attribute es requerido',
            'max' => 'El :attribute no puedo tener mas de :max caracteres'
        ];

        $this->validate($request, $data, $message);

        $dataentErprise = request()->except(['_token', '_method']);

        Enterprise::where('id', '=', $id)->update($dataentErprise);

        return redirect('enterprise')->with('mensaje', 'Operacion editada con exito')->with('tittle', static::$tittle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Enterprise  $enterprise
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $enterprise = Enterprise::findOrFail($id);

        Enterprise::destroy($id);

        return redirect('enterprise')->with('mensaje', 'Operacion eliminada')->with('tittle', static::$tittle);
    }
}
