<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Notifications\Notificationalert;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['quantity', 'id_stockcenter', 'id_article', 'quantity_alert'];

    public function StockCenter()
    {
        return $this->belongsTo(Stockcenter::class, 'id_stockcenter');
    }

    public function Article()
    {
        return $this->belongsTo(Article::class, 'id_article')->orderBy('name', 'asc');
    }

    public function scopeStockCenters($query, $stockcenter)
    {
        if ($stockcenter && $stockcenter != '*') {
            return $query->where('id_stockcenter', '=', $stockcenter);
        }
    }

    public function scopeArticles($query, $articleTX)
    {
        if ($articleTX && $articleTX != '') {
            $articles  = Article::select('id')->where('name', 'LIKE', '%' . $articleTX . '%')->get();
            $article = [];
            foreach ($articles as $key => $value) {
                $article[] = $value->id;
            }
            // dd($article);
            return $query->whereIn('id_article', $article );
        }
    }

    public function scopeType($query, $typeTx)
    {
        if ($typeTx && $typeTx != '') {
            $articles  = Article::select('id')->where('type', 'LIKE', '%' . $typeTx . '%')->get();
            $article = [];
            
            foreach ($articles as $key => $value) {
                $article[] = $value->id;
            }
            // dd($article);
            return $query->whereIn('id_article', $article );
        }
    }

    public function scopeCode($query, $codeTx)
    {
        if ($codeTx && $codeTx != '') {
            $articles  = Article::select('id')->where('code', 'LIKE', '%' . $codeTx . '%')->get();
            $article = [];
            foreach ($articles as $key => $value) {
                $article[] = $value->id;
            }
            // dd($article);
            return $query->whereIn('id_article', $article );
        }
    }

    public function getWarningAttribute(){
        // if ($this->quantity_alert == 0) return;
        return $this->quantity_alert >= $this->quantity ? true : false;

    }

    public function updateAlert($type){
        // type = true suman objetos se eliminar alerta
        // type = false restan objetos se crea la alerta
        $user = Auth::user();
        if($user->notifications->count()){
            $user->notifications->each(function ($notificacion) use ($type, $user) {
                // Acceder al campo "data" de la notificación
                $datosNotificacion = $notificacion->data;

                // Verificar si el campo "article_id" y "stockcenter_id" son iguales
                if ($datosNotificacion['stockcenter_id'] == $this->id_stockcenter && 
                    $datosNotificacion['article_id'] == $this->id_article) {
                    
                    // Eliminar notificacion si se elimina la alerta
                    if ($this->quantity_alert < $this->quantity && $type){
                    $notificacion->delete();
                    }
                } else if ($this->quantity_alert >= $this->quantity && !$type){
                    // Crear alerta si no existe una creada
                    $data = [
                        'menssage' => 'El material '. $this->Article->name . ' se encuentra por debajo del nivel de stock',
                        'article_id' => $this->id_article,
                        'stockcenter_id' => $this->id_stockcenter
                    ];
                    
                    $user->notify(new Notificationalert($data));
                }
            }
        );} else{
            // Crear alerta si no existe una creada
            $data = [
                'menssage' => 'El material '. $this->Article->name . ' se encuentra por debajo del nivel de stock',
                'article_id' => $this->id_article,
                'stockcenter_id' => $this->id_stockcenter
            ];
            
            $user->notify(new Notificationalert($data));
        }
    }
}
