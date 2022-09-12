<?php

namespace App\Exports;

use App\Models\Stock;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StocksExport implements FromView
{
    public function view(): View
    {
        $data['stockselect'] = request()->get('stockselect');
        $data['type'] = request()->get('type');
        $data['articlename'] = request()->get('articlename');
        $data['code'] = request()->get('code');
        
        return view('exports.stocks', [
            'stocks' => Stock::StockCenters($data['stockselect'])
            ->Type($data['type'])
            ->Articles($data['articlename'])
            ->Code($data['code'])
            ->orderBy('id_stockcenter', 'asc')
            ->leftJoin('articles', 'stocks.id_article', '=', 'articles.id')
            ->orderBy('articles.name', 'asc')
            ->get()
        ]);
    }
}
