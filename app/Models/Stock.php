<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['quantity', 'id_stockcenter', 'id_article'];

    public function StockCenter()
    {
        return $this->belongsTo(Stockcenter::class, 'id_stockcenter');
    }

    public function Article()
    {
        return $this->belongsTo(Article::class, 'id_article');
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
            foreach ($articles as $key => $value) {
                $article[] = $value->id;
            }
            // dd($article);
            return $query->whereIn('id_article', $article );
        }
    }
}
