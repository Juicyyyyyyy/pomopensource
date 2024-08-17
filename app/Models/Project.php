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

    public function updateTimeFocusedProject()
    {
        $tasks = $this->getTasks();
        // update time_focused_on_tasks
        $this->update(['time_focused_on_tasks' => $tasks->sum('time_focused')]);
        // update time_focused_total
        $this->update(['time_focused_total' => $this->getAttribute('time_focused_on_project_only') + $this->getAttribute('time_focused_on_tasks')]);
    }


}
