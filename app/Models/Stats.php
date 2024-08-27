<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Stats extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'minute_focused',
        'days_accessed',
        'day_streak',
    ];

    protected $casts = [
        'minute_focused' => 'integer',
        'days_accessed' => 'integer',
        'day_streak' => 'integer',
    ];

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
