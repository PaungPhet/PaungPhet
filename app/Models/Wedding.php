<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Wedding extends Model
{
    protected $fillable = [
        'user_id',
        'partner_one',
        'partner_two',
        'slug',
        'content',
        'event_date',
        'event_time',
        'address',
        'address_url',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
        ];
    }
}
