<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OperationConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'operation_id',
        'request_origin_id',
        'consume_destiny_id'
    ];

    public function operation()
    {
        return $this->belongsTo(Operation::class);
    }

    public function requestOrigin()
    {
        return $this->belongsTo(Stockcenter::class, 'request_origin_id');
    }

    public function consumeDestiny()
    {
        return $this->belongsTo(Stockcenter::class, 'consume_destiny_id');
    }
}
