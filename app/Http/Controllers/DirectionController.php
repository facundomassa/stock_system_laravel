<?php

namespace App\Http\Controllers;

use App\Models\Direction;
use Illuminate\Http\Request;
use App\Http\Controllers\api\ApiCountryController;

class DirectionController extends Controller
{
    protected static $title = 'Direcciones';

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

    public function index()
    {
        $data['directions'] = Direction::paginate(20);
        return view('direction/index')->with($data)->with('title', static::$title);
    }

    public function create()
    {
        $api = new ApiCountryController();
        $response = $api->country();
        $countries = json_decode($response->getContent(), true);

        $data = [
            'direction' => new Direction(), // Empty model for the form
            'countries' => $countries, // Array of countries for the dropdown
            'title' => static::$title,
        ];

        return view('direction/create', $data);
    }

    public function store(Request $request)
    {
        $this->validate($request, static::$data, static::$message);
        $dataDirection = request()->except('_token');
        Direction::create($dataDirection);
        return redirect('direction')->with('mensaje', 'Direccion agregada con exito')->with('title', static::$title);
    }

    public function show($id)
    {
        $direction = Direction::findOrFail($id);
        return view('direction.show', compact('direction'))->with('title', static::$title);
    }

    public function edit($id)
    {
        $api = new ApiCountryController();
        $response = $api->country();
        $countries = json_decode($response->getContent(), true);
        $direction = Direction::findOrFail($id);

        $data = [
            'direction' => $direction,
            'countries' => $countries,
            'title' => static::$title,
        ];

        return view('direction.edit', $data);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, static::$data, static::$message);
        $dataDirection = request()->except(['_token', '_method']);
        Direction::find($id)->update($dataDirection);
        return redirect('direction')->with('mensaje', 'Direccion editada con exito')->with('title', static::$title);
    }

    public function destroy($id)
    {
        $direction = Direction::findOrFail($id);
        Direction::destroy($id);
        return redirect('direction')->with('mensaje', 'Direccion eliminada')->with('title', static::$title);
    }
}
