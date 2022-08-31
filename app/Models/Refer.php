<?php

namespace App\Models;

use App\Http\Controllers\StockController;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refer extends Model
{
    use HasFactory;

    protected $fillable = ['origen_id_stockcenter', 'destiny_id_stockcenter', 'status', 'date_ended', 'id_user'];

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
    public static function ValidateIDRel(Request $request)
    {
        if (!Stockcenter::find($request->origen_id_stockcenter)->exists()) {
            $request['origen_id_stockcenter'] = null;
        }
        if (!Stockcenter::find($request->destiny_id_stockcenter)->exists()) {
            $request['destiny_id_stockcenter'] = null;
        }
        if (!User::find($request->id_user)->exists()) {
            $request['id_user'] = null;
        }
        return $request;
    }

    public function setDate_endedAttribute($value)
    {
        $this->attributes['date_ended'] = str_replace("T", " ", $value);
    }

    public function setIdUserAttribute()
    {
        $this->attributes['id_user'] = auth()->user()->id;
    }

    //convert nombre of status
    public function getStatusNameAttribute()
    {
        $status = $this->status;
        switch ($status) {
            case 'I':
                return $this->statusName = 'INGRESADO';
                break;
            case 'E':
                return $this->statusName = 'EMITIDO';
                break;
            case 'F':
                return $this->statusName = 'FINALIZADO';
                break;
            case 'C':
                return $this->statusName = 'CANCELADO';
                break;
        }
    }

    public function getFullNameUserAttribute()
    {
        return $this->User->name . " " . $this->User->surname;
    }

    public function getNameOriginAttribute()
    {
        return $this->Origin->name;
    }

    public function getNameDestinyAttribute()
    {
        return $this->Destiny->name;
    }

    public function getCreatedAtFormattedAttribute()
    {
        return $this->created_at->format('d/m/Y');
    }

    public function getDateEndedFormattedAttribute($value)
    {
        if($this->date_ended != null){
            $timestamp = strtotime($this->date_ended); 
            return date('d/m/Y', $timestamp );
        }
    }

    public function canceled(){
        $this->status = 'C';

        $this->update($this->attributes);
    }

    public function emited(){
        $this->status = 'E';
        if($this->Origin->type != 'P'){
            $movements = Movement::where('id_refer', $this->id)->get();
            StockController::discount($this->attributes, $movements);
        }

        $this->update($this->attributes);
    }

    public function finalized(){
        $this->status = 'F';
        if($this->Destiny->type != 'C'){
            $movements = Movement::where('id_refer', $this->id)->get();
            StockController::increase($this->attributes, $movements);
        }

        $this->update($this->attributes);
    }
}
