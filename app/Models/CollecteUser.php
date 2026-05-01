<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class CollecteUser extends Pivot
{
    protected $table = 'collecte_user';

    protected $casts = [
        'joined_at' => 'datetime',
    ];

    protected $fillable = ['user_id', 'collecte_id', 'role', 'joined_at'];
}
