<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Collecte extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom', 'description', 'created_by', 'status', 'config_schema', 'preprocess_rules','category_field_index'
    ];

    protected $casts = [
        'config_schema' => 'array',
        'preprocess_rules' => 'array',
        'status' => 'string',
    ];

    // Relations
    public function createur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function utilisateurs(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'collecte_user')
                    ->withPivot('role', 'joined_at')
                    ->withTimestamps();
    }

    public function donnees(): HasMany
    {
        return $this->hasMany(DonneeCollecte::class);
    }

    public function invitations(): HasMany
    {
        return $this->hasMany(Invitation::class);
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
    public function getRoleOfUser(User $user): ?string
    {
        $pivot = $this->utilisateurs()->where('user_id', $user->id)->first();
        return $pivot ? $pivot->pivot->role : null;
    }

    public function peutModifierDonnees(User $user): bool
    {
        return $user->isAdmin() || $user->estCreateurDe($this);
    }

    public function peutExporter(User $user): bool
    {
        if ($user->isAdmin() || $user->estCreateurDe($this)) return true;

        $role = $this->getRoleOfUser($user);
        return in_array($role, ['superviseur', 'analyste']);
    }

    public function peutVoirToutesDonnees(User $user): bool
    {
        if ($user->isAdmin() || $user->estCreateurDe($this)) return true;

        $role = $this->getRoleOfUser($user);
        return in_array($role, ['superviseur', 'analyste']);
    }
}
