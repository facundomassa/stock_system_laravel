<?php

namespace App\Http\Controllers;

use App\Models\Operation;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class OperationController extends Controller
{
    protected static $title = 'Operacion';

    private static $data = [
        'name' => 'required|string|max:255',
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
        $data['operations'] = Operation::paginate(20);
        return view('operation/index')->with($data)->with('title', static::$title);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('operation/create')->with('title', static::$title);
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

        $dataOperation = request()->except('_token');

        $operation = Operation::create($dataOperation);

        // Crear un permiso basado en la operación
        $permissionName = $operation->name; 
        
        // Verifica si el permiso ya existe antes de crearlo
        if (!Permission::where('name', $permissionName)->exists()) {
            Permission::create(['name' => $permissionName]);
        }

        return redirect('operation')->with('mensaje', 'Operacion agregada con exito')->with('title', static::$title);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $operation = Operation::findOrFail($id);
        return view('operation.show', compact('operation'))->with('title', static::$title);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $operation = Operation::findOrFail($id);
        return view('operation.edit', compact('operation'))->with('title', static::$title);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, static::$data, static::$message);

        $dataOperation = request()->except(['_token', '_method']);

        Operation::find($id)->update($dataOperation);

        return redirect('operation')->with('mensaje', 'Operacion editada con exito')->with('title', static::$title);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Operation  $operation
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $operation = Operation::findOrFail($id);

        Operation::destroy($id);

        return redirect('operation')->with('mensaje', 'Operacion eliminada')->with('title', static::$title);
    }
}
