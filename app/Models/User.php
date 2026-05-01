<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'password', 'is_admin', 'avatar', 'last_login_at'
    ];

    protected $casts = [
        'is_admin' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    protected $hidden = ['password', 'remember_token'];

    // Relations
    public function collectesCrees(): HasMany
    {
        return $this->hasMany(Collecte::class, 'created_by');
    }

    public function collectes(): BelongsToMany
    {
        return $this->belongsToMany(Collecte::class, 'collecte_user')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    public function donneesSaisies(): HasMany
    {
        return $this->hasMany(DonneeCollecte::class);
    }

    public function invitationsEnvoyees(): HasMany
    {
        return $this->hasMany(Invitation::class, 'invited_by');
    }

    public function exports(): HasMany
    {
        return $this->hasMany(Export::class);
    }

    public function logs(): HasMany
    {
        return $this->hasMany(LogCollecte::class);
    }

    // Méthodes utilitaires
    public function isAdmin(): bool
    {
        return $this->is_admin === true;
    }

    public function aLeRoleDansCollecte(Collecte $collecte, string $role): bool
    {
        $pivot = $this->collectes()->where('collecte_id', $collecte->id)->first();
        return $pivot && $pivot->pivot->role === $role;
    }

    public function estCreateurDe(Collecte $collecte): bool
    {
        return $this->id === $collecte->created_by;
    }
}
