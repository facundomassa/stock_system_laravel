<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['quantity', 'id_stockcenter', 'id_article', 'limit'];

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
        if ($this->limit == 0) return;
        return $this->limit <= $this->quantity ? true : false;

    }
}
