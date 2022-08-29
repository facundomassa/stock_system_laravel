<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Person extends Model
{
    use HasFactory;

    protected $table = 'persons';

    protected $fillable = ['name', 'surname', 'email', 'cuit', 'telephone'];

    //obtener el nombre completo
    public function getFullNameAttribute(){
        return $this->name . ' ' . $this->surname;
    }

    public function setNameAttribute($value){
        return $this->attributes['name'] = ucwords(strtolower($value));
    }

    public function setSurnameAttribute($value){
        return $this->attributes['surname'] = ucwords(strtolower($value));
    }
}
