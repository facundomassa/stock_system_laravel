<?php

namespace App\Models;


use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stockcenter extends Model
{
    use HasFactory;

    public function Operation()
    {
        return $this->belongsTo(Operation::class, 'id_operation');
        
    }

    public function Direction()
    {
        return $this->belongsTo(Direction::class, 'id_direction');
    }

    public function Person()
    {
        return $this->belongsTo(Person::class, 'id_person');
    }

    public function getFullNamePersonAttribute()
    {
        return $this->Person->name . " " . $this->Person->surname;
    }

    public function scopeOperationSelect($query, $operation)
    {
        // Obtener todas las operaciones que coincidan
        $operationRecords = Operation::whereIn('name', $operation)->get();

        // Verificar si se encontraron operaciones
        if ($operationRecords->isNotEmpty()) {
            // Obtener solo los IDs
            $operationIds = $operationRecords->pluck('id');

            // Filtrar por los IDs de las operaciones
            return $query->whereIn('id_operation', $operationIds);
        }

        // Si no hay operaciones coincidentes, no filtrar
        return $query;
    }

    //validate id of related tables
    public static function ValidateIDRel(Request $request){
        if (!Direction::where('id', '=', $request->id_direction)->exists()) {
            $request->merge(['id_direction' => null]);
        }
        if (!Operation::where('id', '=', $request->id_operation)->exists()) {
            $request->merge(['id_operation' => null]);
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
