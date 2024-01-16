<?php

namespace App\Exports;

use App\Models\Refer;
use App\Models\Movement;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class MovementExport implements FromView
{
    public function view(): View
    {
        $data['stc_origin'] = 15;
        $data['stc_destiny'] = '*';
        $data['date_start'] = '2023-12-01';
        $data['date_end'] = '2023-12-31';

        //obtenemos los remitos con los datos entregados
        $refer = Refer::FechaFinalizado($data['date_start'], $data['date_end'])
            ->StockCenterOrigin($data['stc_origin'])
            ->StockCenterDestiny($data['stc_destiny'])
            ->pluck('id')
            ->all();

        /*
        groupBy('id_article') agrupa los resultados por el campo id_article.
        selectRaw('id_article, SUM(quantity) as total_quantity') selecciona el campo id_article y calcula la suma de la columna quantity, nombrándola como total_quantity.
        */     
        $movements = Movement::whereIn('id_refer', $refer)
            ->groupBy('id_article')
            ->selectRaw('id_article, SUM(quantity) as total_quantity')
            ->get();
        
        return view('exports.reportMovement')->with('movements', $movements);
    }
}
