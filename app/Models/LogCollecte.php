<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LogCollecte extends Model
{
    use HasFactory;

    protected $table = 'logs_collecte';

    public $timestamps = false;

    protected $fillable = [
        'user_id', 'collecte_id', 'action', 'metadata', 'created_at'
    ];

    protected $casts = [
        'metadata' => 'array',
        'created_at' => 'datetime',
    ];

    public function utilisateur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function collecte(): BelongsTo
    {
        return $this->belongsTo(Collecte::class);
    }
}
