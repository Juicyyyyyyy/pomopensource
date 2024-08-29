<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['category_id', 'key', 'name', 'type', 'options', 'description', 'default_value', 'display_order'];

    protected $casts = [
        'options' => 'array',
        'key' => 'string'
    ];

    public function category()
    {
        return $this->belongsTo(SettingCategory::class, 'category_id');
    }

    public function userSettings(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(UserSetting::class);
    }
}
