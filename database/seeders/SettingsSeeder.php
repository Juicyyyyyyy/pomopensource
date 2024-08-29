<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        // Seed setting categories
        $categories = [
            ['id' => 1, 'name' => 'General', 'icon' => 'fas fa-cog', 'display_order' => 1],
            ['id' => 2, 'name' => 'Timers', 'icon' => 'fas fa-clock', 'display_order' => 2],
            ['id' => 3, 'name' => 'Sound', 'icon' => 'fas fa-volume-up', 'display_order' => 3],
        ];

        foreach ($categories as $category) {
            DB::table('setting_categories')->insertOrIgnore($category);
        }

        // Seed settings
        $settings = [
            // General settings
            [
                'category_id' => 1,
                'key' => 'theme',
                'name' => 'Theme',
                'type' => 'select',
                'options' => json_encode(['Lofi Cofee', 'Seoul Sunrise', 'Parisian Cat']),
                'default_value' => 'Parisian Cat',
                'display_order' => 1,
            ],
            // Timer settings
            [
                'category_id' => 2,
                'key' => 'pomodoro_duration',
                'name' => 'Pomodoro',
                'type' => 'number',
                'options' => null,
                'default_value' => '25',
                'display_order' => 1,
            ],
            [
                'category_id' => 2,
                'key' => 'short_break_duration',
                'name' => 'Short Break',
                'type' => 'number',
                'options' => null,
                'default_value' => '5',
                'display_order' => 2,
            ],
            [
                'category_id' => 2,
                'key' => 'long_break_duration',
                'name' => 'Long Break',
                'type' => 'number',
                'options' => null,
                'default_value' => '15',
                'display_order' => 3,
            ],
            [
                'category_id' => 2,
                'key' => 'auto_start_pomodoros',
                'name' => 'Auto Start Pomodoros',
                'type' => 'checkbox',
                'options' => null,
                'default_value' => 'false',
                'display_order' => 5,
            ],
            // Sound settings
            [
                'category_id' => 3,
                'key' => 'alert_sound',
                'name' => 'Alert Sound',
                'type' => 'select',
                'options' => json_encode(['Bell', 'Birds', 'Lofi']),
                'default_value' => 'Birds',
                'display_order' => 1,
            ],
            [
                'category_id' => 3,
                'key' => 'alert_volume',
                'name' => 'Alert Volume',
                'type' => 'range',
                'options' => null,
                'default_value' => '50',
                'display_order' => 2,
            ],
            [
                'category_id' => 3,
                'key' => 'play_sound',
                'name' => 'Play sound when timer finishes',
                'type' => 'checkbox',
                'options' => null,
                'default_value' => 'true',
                'display_order' => 3,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('settings')->insertOrIgnore($setting);
        }
    }
}
