<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Stock extends Model
{
    use HasFactory;

    protected $fillable = ['quantity', 'id_stockcenter', 'id_article', 'quantity_alert'];
    
    protected $appends = ['warning'];

    public function stockCenter(): BelongsTo
    {
        return $this->belongsTo(Stockcenter::class, 'id_stockcenter');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article');
    }

    public function scopeStockCenters($query, ?string $stockcenter): void
    {
        if ($stockcenter && $stockcenter !== '*') {
            $query->where('id_stockcenter', $stockcenter);
        }
    }

    public function scopeArticles($query, ?string $articleName): void
    {
        if ($articleName) {
            $articleIds = Article::where('name', 'LIKE', "%{$articleName}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }
    }

    public function scopeType($query, ?string $type): void
    {
        if ($type) {
            $articleIds = Article::where('type', 'LIKE', "%{$type}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }
    }

    public function scopeCode($query, ?string $code): void
    {
        if ($code) {
            $articleIds = Article::where('code', 'LIKE', "%{$code}%")->pluck('id');
            $query->whereIn('id_article', $articleIds);
        }
    }

    public function getWarningAttribute(): bool
    {
        if ($this->quantity_alert == 0) {
            return false;
        }
        
        return $this->quantity <= $this->quantity_alert;
    }
}