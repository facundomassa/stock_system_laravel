<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Direction;
use App\Models\Person;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ImportController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('import.create')->with('title', "Importacion de Datos");;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validar que el archivo y el tipo de modelo se hayan enviado correctamente
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt',
            'model_type' => 'required|string|in:articles,persons,directions', // Opciones de importación permitidas
        ]);

        $file = $request->file('csv_file');
        $modelType = $request->input('model_type');
        $rowCount = 1; // Inicia en 1 para contar la cabecera
        $importedCount = 0;

        DB::beginTransaction();

        try {
            if (($handle = fopen($file->getRealPath(), 'r')) !== FALSE) {
                // Omitir la primera fila (cabecera)
                fgetcsv($handle, 1000, ";");

                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $rowCount++;
                    
                    // Lógica de importación según el tipo de modelo
                    switch ($modelType) {
                        case 'articles':
                            $rowData = [
                                'name' => $data[0] ?? null,
                                'code' => $data[1] ?? null,
                                'unit' => $data[2] ?? null,
                                'type' => $data[3] ?? null,
                            ];
                            $validator = Validator::make($rowData, [
                                'name' => 'required|string|max:60',
                                'code' => 'nullable|string|max:12',
                                'unit' => 'required|string|max:1',
                                'type' => 'nullable|string|max:30',
                            ]);
                            $modelName = 'Artículo';
                            if ($validator->fails()) throw new Exception("Error de validación en la fila {$rowCount}: " . $validator->errors()->first());
                            Article::create($validator->validated());
                            break;

                        case 'persons':
                            $rowData = [
                                'name' => $data[0] ?? null,
                                'surname' => $data[1] ?? null,
                                'email' => $data[2] ?? null,
                                'cuit' => $data[3] ?? null,
                                'telephone' => $data[4] ?? null,
                            ];
                            $validator = Validator::make($rowData, [
                                'name' => 'required|string|max:60',
                                'surname' => 'nullable|string|max:60',
                                'email' => 'nullable|email|max:200',
                                'cuit' => 'nullable|integer',
                                'telephone' => 'nullable|integer',
                            ]);
                             $modelName = 'Persona';
                            if ($validator->fails()) throw new Exception("Error de validación en la fila {$rowCount}: " . $validator->errors()->first());
                            Person::create($validator->validated());
                            break;

                        case 'directions':
                            $rowData = [
                                'country' => $data[0] ?? null,
                                'state' => $data[1] ?? null,
                                'city' => $data[2] ?? null,
                                'locality' => $data[3] ?? null,
                                'street' => $data[4] ?? null,
                                'number' => $data[5] ?? null,
                                'department' => $data[6] ?? null,
                                'house' => $data[7] ?? null,
                                'floor' => $data[8] ?? null,
                                'cp' => $data[9] ?? null,
                            ];
                            $validator = Validator::make($rowData, [
                                'country' => 'required|string|max:60',
                                'state' => 'required|string|max:60',
                                'city' => 'required|string|max:60',
                                'locality' => 'required|string|max:100',
                                'street' => 'required|string|max:100',
                                'number' => 'required|integer',
                                'department' => 'nullable|string|max:6',
                                'house' => 'nullable|string|max:6',
                                'floor' => 'nullable|string|max:4',
                                'cp' => 'required|integer',
                            ]);
                            $modelName = 'Dirección';
                            if ($validator->fails()) throw new Exception("Error de validación en la fila {$rowCount}: " . $validator->errors()->first());
                            Direction::create($validator->validated());
                            break;
                    }
                    $importedCount++;
                }
                fclose($handle);
            }

            DB::commit();

            $pluralizedModel = $importedCount > 1 ? $modelName . 's' : $modelName;
            return redirect()->back()->with('success', "¡Éxito! Se importaron {$importedCount} {$pluralizedModel} correctamente.");

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error en la importación de CSV: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error en la importación: ' . $e->getMessage());
        }
    }
}
