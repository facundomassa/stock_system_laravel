<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Charts\RetiredArticles;
use App\Models\Stock;
use App\Models\Refer;
use App\Models\Article;
use App\Models\Movement;
use App\Models\Stockcenter;

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
        $data['stockcenters'] = Stockcenter::all();
        $data['hoy']= date('Y-m-d');
        $nuevafecha = strtotime('-1 months', strtotime($data['hoy']));
        $data['mes'] = date('Y-m-d' , $nuevafecha);

        //obtenemos opciones de los filtros
        $options = [
            'stockselectorigen' => request()->get('stockselectorigen'),
            'stockselectdestiny' => request()->get('stockselectdestiny')
        ];
        //buscamos sus remitos correspondientes y los convertimos en un array a sus id
        $referData = Refer::StockCenterOrigin($options['stockselectorigen'])
            ->StockCenterDestiny($options['stockselectdestiny'])
            ->FechaCreado($data['mes'],$data['hoy'])
            ->pluck('id')
            ->all();

        $movements = [];
        foreach ($referData as $key => $value){
            $movementData = Movement::where('id_refer', $value)->pluck('quantity','id_article')->all();

            foreach ($movementData as $key2 => $value2){
                if(!array_key_exists($key2, $movements)){
                    $movements[$key2] = $value2;
                }
                else{
                    $movements[$key2] += $value2;
                }
            }
        }

        $movementsName = [];

        foreach ($movements as $id => $valor) {
            // Verificar si hay un nombre asociado al id
            $nombre = Article::find($id);
            // Asignar el valor al nuevo array con la clave cambiada
            $movementsName[$nombre->name] = $valor;
        }

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
        $chart->labels = (array_keys($movementsName));
        $chart->dataset = (array_values($movementsName));
        $chart->colours = $colours;

        return view('home', compact('chart'))
        ->with($data)
        ->with($options);
    }

    public function howtouse()
    {
        return view('home/howtouse')->with('tittle', 'Como Usar');
    }
}
