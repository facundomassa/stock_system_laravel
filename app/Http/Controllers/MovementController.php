<?php

namespace App\Http\Controllers;

use App\Services\MovementService;
use App\Services\ReferService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MovementController extends Controller
{
    protected string $title = 'Movimientos';
    protected MovementService $movementService;
    protected ReferService $referService;

    public function __construct(MovementService $movementService, ReferService $referService)
    {
        $this->movementService = $movementService;
        $this->referService = $referService;
    }

    public function index(): View
    {
        $movements = $this->movementService->paginateMovements();
        
        return view('movement.index', compact('movements'))->with('title', $this->title);
    }

    public function create(int $id): View
    {
        $refer = $this->referService->findRefer($id);
        $movements = $this->movementService->getMovementsByRefer($id);
        $movements = $this->movementService->enrichMovementsWithStockInfo($movements, $refer);
        $movements = $this->movementService->enrichMovementsWithArticleInfo($movements);
        
        $articles = $this->referService->getArticlesWithStockInfo($id);
        // dd($articles->first());
        return view('movement.create', compact('refer', 'movements', 'articles'))
            ->with('title', $this->title);
    }

    public function store(Request $request): RedirectResponse
    {
        $id_refer = $request->id_refer;
        $movementsData = $request->except(['_token', 'id_refer']);

        $cleanedData = [];
        foreach ($movementsData as $key => $data) {
            // A movement is valid if it's being deleted, or if it has a quantity.
            $isDeletion = !empty($data['delete']);
            $hasQuantity = !empty($data['quantity']);

            if ($isDeletion) {
                $cleanedData[$key] = $data;
            } elseif ($hasQuantity) {
                // It's a create or update, ensure quantity is an integer.
                $data['quantity'] = (int) $data['quantity'];
                $cleanedData[$key] = $data;
            }
            // Ignore entries that are not for deletion and have no quantity.
        }

        if (empty($cleanedData)) {
            return redirect()->route('refer.show', $id_refer)
                ->with('mensaje', 'No se proporcionaron datos de movimiento válidos.')
                ->with('title', $this->title);
        }
        
        $results = $this->movementService->processBatchMovements($cleanedData, $id_refer);
        
        $message = $this->generateResultMessage($results);

        return redirect()->route('refer.show', $id_refer)
            ->with('mensaje', $message)
            ->with('title', $this->title);
    }

    public function show(int $id_refer): View
    {
        $refer = $this->referService->findRefer($id_refer);
        $movements = $this->movementService->getMovementsByRefer($id_refer);
        $movements = $this->movementService->enrichMovementsWithArticleInfo($movements);
        
        return view('movement.show', compact('refer', 'movements'))->with('title', $this->title);
    }

    public function destroy(int $id): RedirectResponse
    {
        $this->movementService->deleteMovement($id);
        
        return redirect()->route('movement.index')
            ->with('mensaje', 'Movimiento eliminado correctamente')
            ->with('title', $this->title);
    }

    public function transit(): View
    {
        $paginatedResults = $this->movementService->getTransitMovements();
        
        $refer = [];
        foreach ($paginatedResults as $referId => $movements) {
            $refer[$referId] = $this->referService->findRefer($referId);
        }
        
        return view('movement.transit', compact('paginatedResults', 'refer'))
            ->with('title', 'Movimientos en Tránsito');
    }

    private function generateResultMessage(array $results): string
    {
        
        $message = "Se eliminaron {$results['deleted']} de un total de {$results['deleted_total']} - " .
                   "Se actualizaron {$results['updated']} de un total de {$results['updated_total']} - " .
                   "Se crearon {$results['created']} de un total de {$results['created_total']}";
        
        if (!empty($results['errors'])) {
            $message .= "<br>Errores encontrados:<br>" . implode('<br>', $results['errors']);
        }
        // dd($message);
        return $message;
    }
}
