<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\SettingCategory;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserSettingsController extends Controller
{
    public function index(Request $request)
    {
        $categories = SettingCategory::with(['settings' => function ($query) {
            $query->orderBy('display_order');
        }])
            ->orderBy('display_order')
            ->get();

        $settings = $this->getSettings($request);

        $formattedSettings = $categories->map(function ($category) use ($settings) {
            return [
                'name' => $category->name,
                'icon' => $category->icon,
                'settings' => $category->settings->map(function ($setting) use ($settings) {
                    return [
                        'id' => $setting->id,
                        'key' => $setting->key,
                        'name' => $setting->name,
                        'type' => $setting->type,
                        'options' => $setting->options,
                        'value' => $settings[$setting->id] ?? $setting->default_value,
                    ];
                }),
            ];
        });

        return response()->json($formattedSettings);
    }

    public function getBackground(Request $request)
    {
        $settings = $this->getSettings($request);
        $themeSetting = Setting::where('key', 'theme')->first();
        return $settings[$themeSetting->id] ?? $themeSetting->default_value;
    }

    public function update(Request $request)
    {
        $settings = $request->validate([
            'settings' => 'required|array',
            'settings.*.id' => 'required|exists:settings,id',
            'settings.*.value' => 'required',
        ]);

        if (Auth::check()) {
            $this->updateAuthenticatedUserSettings(Auth::user(), $settings['settings']);
        } else {
            $this->updateSessionSettings($request, $settings['settings']);
        }

        return response()->json(['message' => 'Settings updated successfully']);
    }

    private function getSettings(Request $request)
    {
        if (Auth::check()) {
            return Auth::user()->settings()->pluck('value', 'setting_id')->toArray();
        } else {
            return $request->session()->get('user_settings', []);
        }
    }

    private function updateAuthenticatedUserSettings($user, $settings)
    {
        foreach ($settings as $setting) {
            UserSetting::updateOrCreate(
                ['user_id' => $user->id, 'setting_id' => $setting['id']],
                ['value' => $setting['value']]
            );
        }
    }

    private function updateSessionSettings(Request $request, $settings)
    {
        $sessionSettings = $request->session()->get('user_settings', []);
        foreach ($settings as $setting) {
            $sessionSettings[$setting['id']] = $setting['value'];
        }
        $request->session()->put('user_settings', $sessionSettings);
    }
}
