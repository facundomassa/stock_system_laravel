<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'type', 'unit', 'code'];

    private static $unitRule = array('U', 'u', 'K', 'k', 'M', 'm');

    public function setUnitAttribute($value)
    {
        $this->attributes['unit'] = strtoupper($value);
    }

    //validar si la letra existe
    public static function validateUnit($value)
    {
        if (in_array($value, static::$unitRule)) {
            return $unit = strtoupper($value);
        } else {
            return $unit = null;
        }
    }

    //convertir unidad a su nombre
    public function getUnitNameAttribute()
    {
        $unit = $this->unit;
        switch ($unit) {
            case 'U':
                return $this->unit = 'Unidad';
                break;
            case 'M':
                return $this->unit = 'Metro';
                break;
            case 'K':
                return $this->unit = 'Kilogramo';
                break;
        }
    }

    public function scopeName($query, $nameTx){
        if ($nameTx && $nameTx != '') {
            return $query->where('name', 'LIKE', '%' . $nameTx . '%');
        }
    }

    public function scopeType($query, $typeTx){
        if ($typeTx && $typeTx != '') {
            return $query->where('type', 'LIKE', '%' . $typeTx . '%');
        }
    }

    public function scopeCode($query, $codeTx){
        if ($codeTx && $codeTx != '') {
            return $query->where('code', 'LIKE', '%' . $codeTx . '%');
        }
    }

    public function StockQuantity($refer){
        return $this->stock = Stock::select('quantity')->where('id_stockcenter', $refer->origen_id_stockcenter)
        ->where('id_article', $this->id)->first();
    }
}
