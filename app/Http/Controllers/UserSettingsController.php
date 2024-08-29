<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SettingCategory;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    public function index()
    {
        $categories = SettingCategory::with(['settings' => function ($query) {
            $query->orderBy('display_order');
        }])
            ->orderBy('display_order')
            ->get();

        $userSettings = Auth::user()->settings()->pluck('value', 'setting_id');

        $formattedSettings = $categories->map(function ($category) use ($userSettings) {
            return [
                'name' => $category->name,
                'icon' => $category->icon,
                'settings' => $category->settings->map(function ($setting) use ($userSettings) {
                    return [
                        'id' => $setting->id,
                        'key' => $setting->key,
                        'name' => $setting->name,
                        'type' => $setting->type,
                        'options' => $setting->options,
                        'value' => $userSettings[$setting->id] ?? $setting->default_value,
                    ];
                }),
            ];
        });

        return response()->json($formattedSettings);
    }

    public function getBackground()
    {
        $background = UserSetting::where('user_id', Auth::id())
            ->whereHas('setting', function ($query) {
                $query->where('key', 'theme');
            })
            ->with('setting')
            ->first();

        return $background["value"];

    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $settings = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:settings,id',
            'settings.*.value' => 'required',
        ]);

        foreach ($settings['settings'] as $setting) {
            UserSetting::updateOrCreate(
                ['user_id' => $user->id, 'setting_id' => $setting['id']],
                ['value' => $setting['value']]
            );
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }
}
