<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobLog extends Model
{
    /** @var array<int, string> */
    protected $fillable = [
        'job_name',
        'related_id',
        'status',
        'error_message',
        'started_at',
        'finished_at',
    ];

    /** @var array<string, string> */
    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];
}
