<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'unit', 'code'];
    
    protected $appends = ['unit_name'];

    public function setUnitAttribute(string $value): void
    {
        $this->attributes['unit'] = strtoupper($value);
    }

    public function getUnitNameAttribute(): string
    {
        return match($this->unit) {
            'U' => 'Unidad',
            'M' => 'Metro',
            'K' => 'Kilogramo',
            default => ''
        };
    }

    public function scopeName($query, ?string $name)
    {
        return $name ? $query->where('name', 'LIKE', "%{$name}%") : $query;
    }

    public function scopeType($query, ?string $type)
    {
        return $type ? $query->where('type', 'LIKE', "%{$type}%") : $query;
    }

    public function scopeCode($query, ?string $code)
    {
        return $code ? $query->where('code', 'LIKE', "%{$code}%") : $query;
    }

    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class, 'id_article');
    }
}