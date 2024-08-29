<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'icon', 'display_order'];

    public function settings()
    {
        return $this->hasMany(Setting::class, 'category_id');
    }
}
