<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;

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

    // Scopes mejorados con type hints
    public function scopeStockCenters(Builder $query, ?string $stockcenter): Builder
    {
        if ($stockcenter && $stockcenter !== '*') {
            return $query->where('id_stockcenter', $stockcenter);
        }
        return $query;
    }

    public function scopeArticles(Builder $query, ?string $articleName): Builder
    {
        if ($articleName) {
            $articleIds = Article::where('name', 'LIKE', "%{$articleName}%")->pluck('id');
            return $query->whereIn('id_article', $articleIds);
        }
        return $query;
    }

    public function scopeType(Builder $query, ?string $type): Builder
    {
        if ($type) {
            $articleIds = Article::where('type', 'LIKE', "%{$type}%")->pluck('id');
            return $query->whereIn('id_article', $articleIds);
        }
        return $query;
    }

    public function scopeCode(Builder $query, ?string $code): Builder
    {
        if ($code) {
            $articleIds = Article::where('code', 'LIKE', "%{$code}%")->pluck('id');
            return $query->whereIn('id_article', $articleIds);
        }
        return $query;
    }

    public function scopeWithRelations(Builder $query): Builder
    {
        return $query->with(['article', 'stockCenter']);
    }

    public function scopeOrderDefault(Builder $query): Builder
    {
        return $query->orderBy('id_stockcenter')->orderBy('id_article');
    }

    public function getWarningAttribute(): bool
    {
        if ($this->quantity_alert == 0) {
            return false;
        }
        
        return $this->quantity <= $this->quantity_alert;
    }
}