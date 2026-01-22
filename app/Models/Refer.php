<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Refer extends Model
{
    use HasFactory;

    protected $fillable = [
        'origen_id_stockcenter', 
        'destiny_id_stockcenter', 
        'date_up', 
        'status', 
        'date_ended', 
        'id_user', 
        'observation'
    ];

    protected $appends = [
        'status_name',
        'full_name_user',
        'name_origin',
        'name_destiny',
        'created_at_formatted',
        'date_ended_formatted',
        'date_up_formatted'
    ];

    public function origin(): BelongsTo
    {
        return $this->belongsTo(Stockcenter::class, 'origen_id_stockcenter');
    }

    public function destiny(): BelongsTo
    {
        return $this->belongsTo(Stockcenter::class, 'destiny_id_stockcenter');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function movements(): HasMany
    {
        return $this->hasMany(Movement::class, 'id_refer');
    }

    protected function statusName(): Attribute
    {
        return Attribute::make(
            get: fn () => match($this->status) {
                'I' => 'INGRESADO',
                'E' => 'EMITIDO',
                'F' => 'FINALIZADO',
                'C' => 'CANCELADO',
                default => $this->status,
            }
        );
    }

    protected function fullNameUser(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->user ? $this->user->name . ' ' . $this->user->surname : ''
        );
    }

    protected function nameOrigin(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->origin ? $this->origin->name : ''
        );
    }

    protected function nameDestiny(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->destiny ? $this->destiny->name : ''
        );
    }

    protected function createdAtFormatted(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at ? $this->created_at->format('d/m/Y') : ''
        );
    }

    protected function dateEndedFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->date_ended) {
                    return null;
                }
                
                try {
                    return \Carbon\Carbon::parse($this->date_ended)->format('d/m/Y');
                } catch (\Exception $e) {
                    return null;
                }
            }
        );
    }

    protected function dateUpFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->date_up) {
                    return null;
                }
                
                try {
                    return \Carbon\Carbon::parse($this->date_up)->format('d/m/Y');
                } catch (\Exception $e) {
                    return null;
                }
            }
        );
    }

    // Scopes
    public function scopeStockCenterOrigin($query, ?string $stockcenter): void
    {
        if ($stockcenter && $stockcenter !== '*') {
            $query->where('origen_id_stockcenter', $stockcenter);
        }
    }

    public function scopeStockCenterDestiny($query, ?string $stockcenter): void
    {
        if ($stockcenter && $stockcenter !== '*') {
            $query->where('destiny_id_stockcenter', $stockcenter);
        }
    }

    public function scopeStockCenterInDestiny($query, ?iterable $stockcenters): void
    {
        if ($stockcenters && !empty($stockcenters)) {
            $query->whereIn('destiny_id_stockcenter', $stockcenters);
        }
    }

    public function scopeStatus($query, ?string $status): void
    {
        if ($status && $status !== '*') {
            $query->where('status', $status);
        }
    }

    public function scopeFechaCreado($query, ?string $dateStart = null, ?string $dateEnd = null): void
    {
        if ($dateStart && $dateEnd) {
            $query->whereBetween('date_up', [$dateStart, $dateEnd]);
        }
    }

    public function scopeFechaFinalizado($query, ?string $dateStart = null, ?string $dateEnd = null): void
    {
        if ($dateStart && $dateEnd) {
            $query->whereBetween('date_ended', [$dateStart, $dateEnd]);
        }
    }

    // Nota: Los métodos canceled(), emited() y finalized() fueron movidos al servicio
}