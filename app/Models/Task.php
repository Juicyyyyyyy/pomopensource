<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'time_studied'
    ];

    /**
     * Get the projects of a user.
     */
    public function subTasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(SubTask::class, 'task_id');
    }

    /**
     * Calculate total time studied and update time_studied value.
     *
     * @return void
     */
    public function updateTimeStudied(): void
    {
        $totalTime = $this->subTasks()->sum('time_studied');
        $this->update(['time_studied' => $totalTime]);
    }
}
