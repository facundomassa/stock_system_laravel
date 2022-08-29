<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Direction extends Model
{
    use HasFactory;

    protected $fillable = ['country', 'state', 'city', 'locality', 'street', 'number', 'department', 'house', 'floor', 'cp'];

    public function setCityAttribute($value){
        return $this->attributes['city'] = ucwords(strtolower($value));
    }

    public function setLocalityAttribute($value){
        return $this->attributes['locality'] = ucwords(strtolower($value));
    }

    public function setStreetAttribute($value){
        return $this->attributes['street'] = ucwords(strtolower($value));
    }
}
