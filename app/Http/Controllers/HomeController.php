<?php

namespace App\Http\Controllers;

use App\Services\DashboardService;
use App\Exports\ReportRpFySExport;
use App\Exports\MovementExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class HomeController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->middleware('auth');
        $this->dashboardService = $dashboardService;
    }

    public function index(): View
    {
        $user = auth()->user();
        
        // Obtener todas las métricas del dashboard
        $metrics = $this->dashboardService->getDashboardMetrics();
        
        // Obtener datos adicionales
        $notifications = $this->dashboardService->getUserNotifications($user);
        $stockcenters = $this->dashboardService->getStockCenters();
        
        // Obtener movimientos recientes con filtros
        $recentMovements = $this->dashboardService->getRecentMovements([
            'stockselectorigen' => request('stockselectorigen'),
            'stockselectdestiny' => request('stockselectdestiny'),
            'date_start' => request('date_start'),
            'date_end' => request('date_end'),
        ]);
        
        // Preparar datos para gráficos
        $chartData = [
            'labels' => array_keys($recentMovements),
            'data' => array_values($recentMovements),
        ];
        
        return view('home', array_merge($metrics, [
            'notifications' => $notifications,
            'stockcenters' => $stockcenters,
            'chartData' => $chartData,
        ]));
    }

    public function reportRpFyS(Request $request)
    {
        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
            'check_origin' => 'nullable|array',
            'check_destiny' => 'nullable|array',
        ]);
        
        $filters = [
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
            'check_origin' => $request->check_origin,
            'check_destiny' => $request->check_destiny,
        ];
        
        return Excel::download(new ReportRpFySExport($filters), 'reporte_consumo_' . date('Ymd_His') . '.xlsx');
    }

    public function reportAllMovement(Request $request)
    {
        $request->validate([
            'date_start' => 'required|date',
            'date_end' => 'required|date|after_or_equal:date_start',
        ]);
        
        $filters = [
            'date_start' => $request->date_start,
            'date_end' => $request->date_end,
        ];
        
        return Excel::download(new MovementExport($filters), 'movimientos_' . date('Ymd_His') . '.xlsx');
    }

    public function howtouse(): View
    {
        return view('home.howtouse')->with('title', 'Cómo Usar');
    }

    public function operationSelect(Request $request): View|RedirectResponse
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'selected_operation' => 'required',
            ]);
            
            session(['selected_operation' => $request->input('selected_operation')]);
            
            return redirect()->route('home')
                ->with('success', 'Operación seleccionada correctamente');
        }
        
        return view('home.operationSelect')->with('title', 'Seleccionar Operación');
    }
}