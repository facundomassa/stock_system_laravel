<?php

namespace App\Http\Controllers;

use App\Models\Movement;
use App\Models\Refer;
use App\Models\Operation;
use Illuminate\Http\Request;
use App\Models\Stockcenter;
use PDF;

class ReferController extends Controller
{
    protected static $tittle = 'Remito';

    private static $rules = [
        'origen_id_stockcenter' => 'required|integer|digits_between:0,10',
        'destiny_id_stockcenter' => 'required|integer|digits_between:0,10',
        'status' => 'required|string|max:1',
        'observation' => 'nullable|string|max:60',
        'date_ended' => 'nullable|date_format:Y-m-d H:i',
        'date_up' => 'required|date_format:Y-m-d H:i',
        'id_user' => 'required|integer|digits_between:0,10',
    ];
    private static $message = [
        'required' => 'El :attribute es requerido',
        'date_ended.required' => 'Falta fecha finalizado',
        'date_up.required' => 'Debe tener una fecha de inicio',
        'max' => 'El :attribute no puedo tener mas de :max caracteres',
        'after' => 'La fecha no puede ser menor al ingreso de hoy'
    ];

    public function index()
    {
        if(get_selected_operation() == 'admin'){
            $stockActive = Stockcenter::all();
        } else {
            // Filtrar los `Stockcenter` por operaciones permitidas
            $selected_operation[] = get_selected_operation();
            $stockActive = $selected_operation
                ? Stockcenter::OperationSelect($selected_operation)->get()
                : collect(); // Si no hay operaciones permitidas, usar una colección vacía
        }
        
        $data['stockcenters'] = Stockcenter::all();
        $options = [
            'stockselectorigen' => request()->get('stockselectorigen'),
            'stockselectdestiny' => request()->get('stockselectdestiny'),
            'status' => request()->get('status')
        ];

        $data['refers'] = Refer::StockCenterOrigin($options['stockselectorigen'])
        ->StockCenterInDestiny($stockActive->pluck('id'))
        ->StockCenterDestiny($options['stockselectdestiny'])
        ->Status($options['status'])
        ->orderBy('id', 'desc')
        ->paginate(20);
        
        return view('refer/index')
            ->with($data)
            ->with($options)
            ->with('tittle', static::$tittle);
    }

    public function create()
{
        // Verificar operaciones permitidas del usuario
        $allowedOperations = auth()->check() 
            ? auth()->user()->allowedOperations() 
            : collect(); // Si no está autenticado, usar una colección vacía
        
        // Filtrar los `Stockcenter` por operaciones permitidas
        $data['storigens'] = $allowedOperations->isNotEmpty()
            ? Stockcenter::OperationSelect($allowedOperations)->get()
            : collect(); // Si no hay operaciones permitidas, usar una colección vacía

        // Obtener todos los `Stockcenter`
        $data['stockcenters'] = Stockcenter::all();

        // Devolver la vista
        return view('refer/create')
            ->with('tittle', static::$tittle)
            ->with($data);
    }

    public function store(Request $request)
    {
        //
        $request['id_user'] = auth()->user()->id;
        $request['status'] = 'I';

        if (isset($request['date_ended']) && $request['date_ended'] != null) {
            $request['date_ended'] = str_replace("T", " ", $request['date_ended']);
        }
        if (isset($request['date_up']) && $request['date_up'] != null) {
            $request['date_up'] = str_replace("T", " ", $request['date_up']);
        }

        $request = Refer::ValidateIDRel($request);

        $this->validate($request, static::$rules, static::$message);

        $dataRefer = request()->except('_token');

        Refer::create($dataRefer);
        return redirect('refer/' . Refer::latest('id')->first()->id)->with('mensaje', 'Remito creado con exito')->with('tittle', static::$tittle);
    }

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

        if (isset($request['date_ended']) && $request['date_ended'] != null) {
            $request['date_ended'] = str_replace("T", " ", $request['date_ended']);
        }
        if (isset($request['date_up']) && $request['date_up'] != null) {
            $request['date_up'] = str_replace("T", " ", $request['date_up']);
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
        $rules['date_ended'] = 'required:Y-m-d H:i:s';
        $rules['date_up'] = 'required:Y-m-d H:i:s';
        

        $this->validate($request, $rules, static::$message);

        if ($refer->status == "I") {
            $refer->emited();
        }
        $refer->finalized();
        return redirect('refer')->with('mensaje', 'Remito Finalizado')->with('tittle', static::$tittle);
    }

    public function getpdf($id)
    {
        //
        $data['refer'] = Refer::findOrFail($id);
        $data['movements'] = Movement::where('id_refer', $id)->get();

        $refer = Refer::findOrFail($id);
        // dd($data);
        $pdf = PDF::loadView('refer/pdf', $data)->setOptions(['defaultFont' => 'sans-serif']);
        return $pdf->stream('remito_'.$refer->id.'.pdf');
    }
}
