<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonneeCollecte extends Model
{
    use HasFactory;

    protected $table = 'donnees_collectes';

    protected $fillable = [
        'collecte_id', 'user_id', 'data', 'fichiers_processes', 'ip_address'
    ];

    protected $casts = [
        'data' => 'array',
        'fichiers_processes' => 'array',
    ];

    // Relations
    public function collecte(): BelongsTo
    {
        return $this->belongsTo(Collecte::class);
    }

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
