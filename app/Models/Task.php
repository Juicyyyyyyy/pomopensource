<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'time_focused',
        'project_id',
    ];

    protected $casts = [
        'time_focused' => 'integer',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}
