<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnderwritingRule extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'name',
        'rules_json',
        'active',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'rules_json' => 'array',
        'active' => 'boolean',
    ];
}
