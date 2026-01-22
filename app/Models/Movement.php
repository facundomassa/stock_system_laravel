<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = ['quantity', 'id_refer', 'id_article', 'quantity_origen', 'quantity_destiny', 'transit'];

    public function refer(): BelongsTo
    {
        return $this->belongsTo(Refer::class, 'id_refer');
    }

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'id_article');
    }

    public function markAsInTransit(): bool
    {
        return $this->update(['transit' => true]);
    }

    public function markAsOutTransit(): bool
    {
        return $this->update(['transit' => false]);
    }

    public function setQuantityOrigen(): bool
    {
        $origenStockCenterId = $this->refer->origen_id_stockcenter;
        $stock = Stock::where('id_article', $this->id_article)
            ->where('id_stockcenter', $origenStockCenterId)
            ->first();
            
        return $this->update(['quantity_origen' => $stock?->quantity]);
    }

    public function setQuantityDestiny(): bool
    {
        $destinyStockCenterId = $this->refer->destiny_id_stockcenter;
        $stock = Stock::where('id_article', $this->id_article)
            ->where('id_stockcenter', $destinyStockCenterId)
            ->first();
            
        return $this->update(['quantity_destiny' => $stock?->quantity]);
    }
}