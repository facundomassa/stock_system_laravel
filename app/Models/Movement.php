<?php

namespace App\Models;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movement extends Model
{
    use HasFactory;

    protected $fillable = ['quantity', 'id_refer', 'id_article'];

    public function Refer()
    {
        return $this->belongsTo(Refer::class, 'id_refer');
    }

    public function Article()
    {
        return $this->belongsTo(Article::class, 'id_article');
    }

    //validate id of related tables
    public static function ValidateIDRel(Request $request){
        if (!Refer::where('id', '=', $request->id_refer)->exists()) {
            $request->merge(['id_refer' => null]);
        }
        if (!Article::where('id', '=', $request->id_article)->exists()) {
            $request->merge(['id_article' => null]);
        }
        return $request;
    }
}
