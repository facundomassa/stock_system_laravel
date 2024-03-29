<?php

namespace App\Models;

use App\Http\Controllers\StockController;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Refer extends Model
{
    use HasFactory;

    protected $fillable = ['origen_id_stockcenter', 'destiny_id_stockcenter', 'date_up', 'status', 'date_ended', 'id_user', 'observation'];

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

    public function setDate_upAttribute($value)
    {
        $this->attributes['date_up'] = str_replace("T", " ", $value);
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

    public function getDateUpFormattedAttribute($value)
    {
        if($this->date_up != null){
            $timestamp = strtotime($this->date_up); 
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

    public function scopeStockCenterInOrigin($query, $stockcenter)
    {
        if ($stockcenter && $stockcenter != '*') {
            return $query->whereIn('origen_id_stockcenter', $stockcenter);
        }
    }

    public function scopeStockCenterInDestiny($query, $stockcenter)
    {
        if ($stockcenter && $stockcenter != '*') {
            return $query->whereIn('destiny_id_stockcenter', $stockcenter);
        }
    }

    public function scopeStockCenterOrigin($query, $stockcenter)
    {
        if ($stockcenter && $stockcenter != '*') {
            return $query->where('origen_id_stockcenter', $stockcenter);
        }
    }

    public function scopeStockCenterDestiny($query, $stockcenter)
    {
        if ($stockcenter && $stockcenter != '*') {
            return $query->where('destiny_id_stockcenter', $stockcenter);
        }
    }

    public function scopeStatus($query, $status)
    {
        if ($status && $status != '*') {
            return $query->where('status', $status);
        }
    }

    public function scopeFechaCreado($query, $dateStart = '0000-00-00', $dateEnd = '2099-01-01')
    {
        return $query->whereBetween('date_up', [$dateStart,$dateEnd]);
    }

    public function scopeFechaFinalizado($query, $dateStart = 0, $dateEnd = 0)
    {
        return $query->whereBetween('date_ended', [$dateStart,$dateEnd]);
    }
}
