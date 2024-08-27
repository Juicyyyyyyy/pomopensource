<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'minute_focused',
        'project_id',
    ];

    protected $casts = [
        'minute_focused' => 'integer',
    ];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function focusedSessions(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FocusedSession::class);
    }

    public function updateTimeFocused(): void
    {
        $this->minute_focused = $this->focusedSessions()->sum('minute_focused');
        $this->save();
    }
}
