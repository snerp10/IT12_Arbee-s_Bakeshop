<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingsController extends Controller
{
    public function index()
    {
        $defaults = [
            'store.name' => "Arbee's Bakeshop",
            'store.branch' => 'Main',
            'ui.theme' => 'sea',
        ];

        if (!Schema::hasTable('settings')) {
            // Table missing: show defaults to avoid crash; page will prompt user to migrate
            $settings = $defaults;
            return view('admin.settings.index', compact('settings'))
                ->with('error', 'Settings table not found. Please run migrations to enable saving settings.');
        }

        $settings = [
            'store.name' => optional(Setting::find('store.name'))->value ?? $defaults['store.name'],
            'store.branch' => optional(Setting::find('store.branch'))->value ?? $defaults['store.branch'],
            'ui.theme' => optional(Setting::find('ui.theme'))->value ?? $defaults['ui.theme'],
        ];
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'store_name' => 'required|string|max:255',
            'store_branch' => 'required|string|max:255',
            'ui_theme' => 'required|string|in:sea',
        ]);

        if (!Schema::hasTable('settings')) {
            return back()->with('error', 'Settings table not found. Please run migrations first.');
        }

        DB::transaction(function() use ($validated) {
            // Capture previous values in dot-key format
            $before = [
                'store.name'  => optional(Setting::find('store.name'))?->value,
                'store.branch'=> optional(Setting::find('store.branch'))?->value,
                'ui.theme'    => optional(Setting::find('ui.theme'))?->value,
            ];

            // Persist settings using key-value storage
            Setting::updateOrCreate(['key' => 'store.name'],   ['value' => $validated['store_name']]);
            Setting::updateOrCreate(['key' => 'store.branch'], ['value' => $validated['store_branch']]);
            Setting::updateOrCreate(['key' => 'ui.theme'],     ['value' => $validated['ui_theme']]);

            // Prepare new values in dot-key format for consistency
            $after = [
                'store.name'  => $validated['store_name'],
                'store.branch'=> $validated['store_branch'],
                'ui.theme'    => $validated['ui_theme'],
            ];

            // Use a stable sentinel record id (0) for settings singleton
            AuditLog::logAction('update', 'settings', 0, $before, $after, 'Updated system settings');
        });

        return back()->with('success','Settings updated.');
    }
}
