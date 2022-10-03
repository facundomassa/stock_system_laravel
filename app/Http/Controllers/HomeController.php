<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\RetiredArticles;
use App\Models\Stock;
use App\Models\Article;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $stock = [];
        $deposito = array_unique(Stock::pluck('id_article')->all());
        
        foreach ($deposito as $key => $value) {
            $stocksumar = Stock::where('id_article', $value)->pluck('quantity')->all();
            
            $stock[Article::where('id',$value)->first()->name] = array_sum($stocksumar);
        }
        // Generate random colours for the groups
        for ($i = 0; $i <= count($deposito); $i++) {
            $colours[] = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);
        }
        $chart = new RetiredArticles;
        $chart->labels = (array_keys($stock));
        $chart->dataset = (array_values($stock));
        $chart->colours = $colours;

        return view('home', compact('chart'));
    }

    public function howtouse()
    {
        return view('home/howtouse')->with('tittle', 'Como Usar');
    }
}
