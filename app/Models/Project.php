<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'time_studied'
    ];

    /**
     * Get the projects of a user.
     */
    public function tasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Task::class, 'project_id');
    }

    /**
     * Calculate total time studied and update time_studied value.
     *
     * @return void
     */
    public function updateTimeStudied(): void
    {
        $totalTime = $this->tasks()->sum('time_studied');
        $this->update(['time_studied' => $totalTime]);
    }


}
