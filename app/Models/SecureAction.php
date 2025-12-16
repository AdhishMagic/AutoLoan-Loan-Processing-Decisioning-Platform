<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecureAction extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'token',
        'action',
        'expires_at',
        'used_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'expires_at' => 'datetime',
        'used_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, SecureAction>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
