<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApiToken extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'user_id',
        'token',
        'abilities',
        'last_used_at',
        'expires_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'abilities' => 'array',
        'last_used_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * @return BelongsTo<User, ApiToken>
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
