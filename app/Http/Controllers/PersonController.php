<?php

namespace App\Http\Controllers;

use App\Models\Person;
use Illuminate\Http\Request;

class PersonController extends Controller
{
    protected static $tittle = 'Personas';

    private static $data = [
        'name' => 'required|string|max:100',
        'surname' => 'required|string|max:100',
        'email' => 'required|email',
        'cuit' => 'nullable|digits_between:10,11|integer',
        'telephone' => 'nullable|integer|digits_between:6,16',
    ];
    private static $message = [
        'required' => 'El :attribute es requerido',
        'telephone.digits_between' => 'El numero de telefono tiene que ser valido',
        'telephone' => 'El numero de telefono tiene que ser valido',
    ];
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        // $tittle = "Personas";
        $data['persons'] = Person::paginate(20);
        return view('person/index')->with($data)->with('tittle', static::$tittle);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('person/create')->with('tittle', static::$tittle);
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

        $dataPerson = request()->except('_token');

        Person::create($dataPerson);
        return redirect('person')->with('mensaje', 'Persona agregado con exito')->with('tittle', static::$tittle);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $person = Person::findOrFail($id);
        return view('person.show', compact('person'))->with('tittle', static::$tittle);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
        $person = Person::findOrFail($id);
        return view('person.edit', compact('person'))->with('tittle', static::$tittle);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $this->validate($request, static::$data, static::$message);

        $dataPerson = request()->except(['_token', '_method']);

        Person::find($id)->update($dataPerson);
        return redirect('person')->with('mensaje', 'Persona modificada')->with('tittle', static::$tittle);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Person  $person
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $person = Person::findOrFail($id);

        Person::destroy($id);

        return redirect('person')->with('mensaje', 'Persona eliminada')->with('tittle', static::$tittle);
    }
}
