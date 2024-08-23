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
    ];
    private int $time_focused_on_project_only;
    private int $time_focused_on_tasks;

    /**
     * Get the projects of a user.
     */
    public function getTasks(): \Illuminate\Database\Eloquent\Relations\HasMany
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
        $this->update([
            'time_focused_on_tasks' => $this->getTasks()->sum('time_focused'),
            'time_focused_total' => $this->time_focused_on_project_only + $this->time_focused_on_tasks,
        ]);
    }



}
