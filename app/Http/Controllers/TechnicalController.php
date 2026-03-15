<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Stock;

class TechnicalController extends Controller
{
    public function dashboard(Request $request)
    {
        $user = auth()->user();
        $stockcenter = $user->stockcenter;

        if (!$stockcenter) {
            // Redirect to a safe page with an error if no stockcenter is assigned
            return redirect()->route('operation.select')->with('error', 'No tienes un almacén asignado.');
        }

        $stocks = Stock::with('article')->where('id_stockcenter', $stockcenter->id)->paginate(20);

        $alerts = Stock::with('article')
            ->where('id_stockcenter', $stockcenter->id)
            ->where('quantity_alert', '>', 0)
            ->whereRaw('quantity <= quantity_alert')
            ->get();

        $articles = \App\Models\Article::all();
        $consumoStockcenter = \App\Models\Stockcenter::where('type', 'C')->first();

        return view('technical.dashboard', compact('stockcenter', 'stocks', 'alerts', 'articles', 'consumoStockcenter'));
    }

    public function showRequestForm(\App\Services\ReferService $referService)
    {
        $user = auth()->user();

        if (!$user->stockcenter_id) {
            return redirect()->route('operation.select')->with('error', 'No tienes un almacén asignado.');
        }

        $userStockcenter = \App\Models\Stockcenter::with('Operation.config')->find($user->stockcenter_id);
        $operation = $userStockcenter?->Operation;

        $originId = $operation?->config?->request_origin_id;

        if (!$originId) {
            return redirect()->back()->with('error', 'La operación actual no tiene un almacén origen configurado para solicitudes de técnicos.');
        }

        // Buscar un Remito de Solicitud abierto ('I')
        $refer = \App\Models\Refer::where('id_user', $user->id)
            ->where('status', 'I')
            ->where('origen_id_stockcenter', $originId)
            ->where('destiny_id_stockcenter', $user->stockcenter_id)
            ->where('observation', 'Solicitud de materiales')
            ->first();

        if (!$refer) {
            // Crear nuevo Remito de Solicitud
            $referData = [
                'destiny_id_stockcenter' => $user->stockcenter_id,
                'origen_id_stockcenter' => $originId,
                'observation' => 'Solicitud de materiales',
                'date_up' => now()
            ];
            $refer = $referService->createRefer($referData);
        }

        return redirect('movement/create/' . $refer->id);
    }

    public function showConsumeForm(\App\Services\ReferService $referService)
    {
        $user = auth()->user();

        if (!$user->stockcenter_id) {
            return redirect()->route('operation.select')->with('error', 'No tienes un almacén asignado.');
        }

        $userStockcenter = \App\Models\Stockcenter::with('Operation.config')->find($user->stockcenter_id);
        $operation = $userStockcenter?->Operation;

        $destinyId = $operation?->config?->consume_destiny_id;

        if (!$destinyId) {
            return redirect()->back()->with('error', 'La operación actual no tiene un almacén destino configurado para reportes de consumo.');
        }

        // Buscar un Remito de Consumo abierto ('I')
        $refer = \App\Models\Refer::where('id_user', $user->id)
            ->where('status', 'I')
            ->where('origen_id_stockcenter', $user->stockcenter_id)
            ->where('destiny_id_stockcenter', $destinyId)
            ->where('observation', 'Consumo del día')
            ->first();

        if (!$refer) {
            // Crear nuevo Remito de Consumo
            $referData = [
                'origen_id_stockcenter' => $user->stockcenter_id,
                'destiny_id_stockcenter' => $destinyId,
                'observation' => 'Consumo del día',
                'date_up' => now()
            ];
            $refer = $referService->createRefer($referData);
        }

        return redirect('movement/create/' . $refer->id);
    }
}
