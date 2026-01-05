<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReferRequest;
use App\Services\ReferService;
use App\Models\Stockcenter;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use PDF;

class ReferController extends Controller
{
    protected string $title = 'Remito';
    protected ReferService $referService;

    public function __construct(ReferService $referService)
    {
        $this->referService = $referService;
    }

    public function index(): View
    {
        // Obtener destinos permitidos según la operación
        $allowedDestinations = $this->getAllowedDestinations();
        
        $stockcenters = $this->referService->getAllStockcenters();
        
        $refers = $this->referService->paginateRefers(
            originStockCenter: request('stockselectorigen'),
            destinyStockCenter: request('stockselectdestiny'),
            status: request('status'),
            allowedDestinations: $allowedDestinations,
            perPage: 20
        );
       
        $filters = [
            'stockselectorigen' => request('stockselectorigen'),
            'stockselectdestiny' => request('stockselectdestiny'),
            'status' => request('status'),
        ];

        return view('refer.index', compact('stockcenters', 'refers', 'filters'))
            ->with('title', $this->title);
    }

    public function create(): View
    {
        $allowedOperations = get_allowed_operations() ?? collect();
        $origins = $this->referService->getAvailableOrigins($allowedOperations);
        $stockcenters = $this->referService->getAllStockcenters();

        return view('refer.create', compact('origins', 'stockcenters'))
            ->with('title', $this->title);
    }

    public function store(ReferRequest $request): RedirectResponse
    {
        $request->prepareForValidation();
        $refer = $this->referService->createRefer($request->validated());

        return redirect()->route('refer.show', $refer->id)
            ->with('success', 'Remito creado con éxito')
            ->with('title', $this->title);
    }

    public function show(int $id): View
    {
        $data = $this->referService->getReferWithMovements($id);

        return view('refer.show', $data)
            ->with('title', $this->title);
    }

    public function edit(int $id): View
    {
        $refer = $this->referService->findRefer($id);
        $stockcenters = $this->referService->getAllStockcenters();
        
        return view('refer.edit', compact('refer', 'stockcenters'))
            ->with('title', $this->title);
    }

    public function update(ReferRequest $request, int $id): RedirectResponse
    {
        $this->referService->updateRefer($id, $request->validated());

        return redirect()->route('refer.index')
            ->with('success', 'Remito editado con éxito')
            ->with('title', $this->title);
    }

    public function destroy(int $id): RedirectResponse
    {
        try {
            $this->referService->cancelRefer($id);
            return redirect()->route('refer.index')
                ->with('success', 'Remito cancelado')
                ->with('title', $this->title);
        } catch (\Exception $e) {
            return redirect()->route('refer.index')
                ->with('error', $e->getMessage())
                ->with('title', $this->title);
        }
    }

    public function emited(int $id): RedirectResponse
    {
        try {
            $this->referService->emitRefer($id);
            return redirect()->route('refer.index')
                ->with('success', 'Remito emitido')
                ->with('title', $this->title);
        } catch (\Exception $e) {
            return redirect()->route('refer.index')
                ->with('error', $e->getMessage())
                ->with('title', $this->title);
        }
    }

    public function finalized(int $id): RedirectResponse
    {
        try {
            $this->referService->finalizeRefer($id);
            return redirect()->route('refer.index')
                ->with('success', 'Remito finalizado')
                ->with('title', $this->title);
        } catch (\Exception $e) {
            return redirect()->route('refer.index')
                ->with('error', $e->getMessage())
                ->with('title', $this->title);
        }
    }

    public function getPdf(int $id)
    {
        $data = $this->referService->getReferForPdf($id);

        $pdf = PDF::loadView('refer.pdf', $data)
            ->setOptions(['defaultFont' => 'sans-serif']);

        return $pdf->stream('remito_' . $id . '.pdf');
    }

    private function getAllowedDestinations(): \Illuminate\Support\Collection
    {
        if (get_selected_operation() === 'admin') {
            return Stockcenter::all()->pluck('id');
        }

        $selectedOperation = [get_selected_operation()];
        return Stockcenter::OperationSelect($selectedOperation)
            ->get()
            ->pluck('id');
    }
}