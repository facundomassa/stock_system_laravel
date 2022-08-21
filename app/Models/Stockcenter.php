<?php

namespace App\Models;


use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockcenter extends Model
{
    use HasFactory;

    public function Enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'id_enterprise');
        
    }

    public function Direction()
    {
        return $this->belongsTo(Direction::class, 'id_direction');
    }

    public function Person()
    {
        return $this->belongsTo(Person::class, 'id_person');
    }

    //validate id of related tables
    public static function ValidateIDRel(Request $request){
        if (!Direction::where('id', '=', $request->id_direction)->exists()) {
            $request->merge(['id_direction' => null]);
        }
        if (!Enterprise::where('id', '=', $request->id_enterprise)->exists()) {
            $request->merge(['id_enterprise' => null]);
        }
        if (!Person::where('id', '=', $request->id_person)->exists()) {
            $request->merge(['id_person' => null]);
        }
        return $request;
    }

    //convert nombre of type
    public function TypeOf()
    {
        $type = $this->type;
        switch ($type) {
            case 'D':
                return $this->type = 'DEPOSITO';
                break;
            case 'M':
                return $this->type = 'MOVIL';
                break;
            case 'T':
                return $this->type = 'TALLER';
                break;
            case 'C':
                return $this->type = 'CONSUMO';
                break;
            case 'P':
                return $this->type = 'PROVEEDOR';
                break;
        }
    }
}
