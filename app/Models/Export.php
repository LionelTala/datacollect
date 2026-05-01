<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Export extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'collecte_id', 'format', 'type', 'filters_applied', 'file_path', 'row_count'
    ];

    protected $casts = [
        'filters_applied' => 'array',
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
