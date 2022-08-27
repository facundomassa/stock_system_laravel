<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refer extends Model
{
    use HasFactory;

    public function Origin()
    {
        return $this->belongsTo(Stockcenter::class, 'origen_id_stockcenter');
    }
    
    public function Destiny()
    {
        return $this->belongsTo(Stockcenter::class, 'destiny_id_stockcenter');
    }

    public function User()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    //validate id of related tables
    public static function ValidateIDRel(Request $request){
        if (!Stockcenter::where('id', '=', $request->origen_id_stockcenter)->exists()) {
            $request->merge(['origen_id_stockcenter' => null]);
        }
        if (!Stockcenter::where('id', '=', $request->destiny_id_stockcenter)->exists()) {
            $request->merge(['destiny_id_stockcenter' => null]);
        }
        if (!User::where('id', '=', $request->id_person)->exists()) {
            $request->merge(['id_user' => null]);
        }
        return $request;
    }

    //convert nombre of status
    public function StatusOf()
    {
        $status = $this->status;
        switch ($status) {
            case 'I':
                return $this->status = 'INGRESADO';
                break;
            case 'E':
                return $this->status = 'EMITIDO';
                break;
            case 'F':
                return $this->status = 'FINALIZADO';
                break;
            case 'C':
                return $this->status = 'CANCELADO';
                break;
        }
    }
}
