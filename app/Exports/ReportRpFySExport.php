<?php

namespace App\Exports;

use App\Models\Refer;
use App\Models\Movement;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ReportRpFySExport implements FromView
{
    public function view(): View
    {
        $data['stc_origin'] = request()->get('check_origin') ?: '*';
        $data['stc_destiny'] = request()->get('check_destiny') ?: '*';
        $data['date_start'] = request()->get('date_start');
        $data['date_end'] = request()->get('date_end');
        
        //obtenemos los remitos con los datos entregados
        $refer = Refer::FechaFinalizado($data['date_start'], $data['date_end'])
            ->StockCenterInOrigin($data['stc_origin'])
            ->StockCenterInDestiny($data['stc_destiny'])
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
        
        return view('exports.reportRpFyS')->with('movements', $movements);
    }
}
