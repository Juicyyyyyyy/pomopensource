<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'minute_focused',
    ];

    protected $casts = [
        'minute_focused' => 'integer',
    ];


    /**
     * Get the projects of a user.
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    /**
     * Get this project owner
     *
     * @return BelongsTo
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function updateTimeFocusedProject(): void
    {
        $this->minute_focused= $this->tasks->sum('minute_focused');
        $this->save();
    }



}
