<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Operation;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'surname',
        'email',
        'password',
        'is_active',
        'stockcenter_id'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function allowedOperations()
    {
        // dd($this->getPermissionNames());
        return Operation::select('name')->whereIn('name', $this->getAllPermissions()->pluck('name'))->get();
    }

    // Scope para obtener solo los usuarios activos
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope para obtener solo los usuarios inactivos
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function stockcenter()
    {
        return $this->belongsTo(Stockcenter::class, 'stockcenter_id');
    }
}
