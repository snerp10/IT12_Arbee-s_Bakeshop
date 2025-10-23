<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function index()
    {
        return view('admin.backup.index');
    }

    public function download()
    {
        // For SQLite dev: return the DB file; for MySQL, this would require mysqldump configured.
        $connection = config('database.default');
        $cfg = config("database.connections.$connection");

        if ($cfg['driver'] === 'sqlite') {
            $path = database_path('database.sqlite');
            AuditLog::logAction('backup','database', null, null, null, 'Downloaded SQLite database backup');
            return response()->download($path, 'backup_'.now()->format('Ymd_His').'.sqlite');
        }

        // MySQL basic export fallback: export logical dump route not implemented
        // You can integrate spatie/laravel-backup or run mysqldump here if available.
        return back()->with('error','Backup for this DB driver is not configured.');
    }

    public function restore(Request $request)
    {
        $request->validate(['backup_file' => 'required|file']);
        $connection = config('database.default');
        $cfg = config("database.connections.$connection");

        if ($cfg['driver'] === 'sqlite') {
            $file = $request->file('backup_file');
            $dest = database_path('database.sqlite');
            // Put site into maintenance mode during restore
            Artisan::call('down');
            try {
                $backupContent = file_get_contents($file->getRealPath());
                file_put_contents($dest, $backupContent);
                AuditLog::logAction('restore','database', null, null, null, 'Restored SQLite database from upload');
            } finally {
                Artisan::call('up');
            }
            return back()->with('success','Database restored successfully.');
        }

        return back()->with('error','Restore for this DB driver is not configured.');
    }
}
