<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Response;

class DatabaseBackupController extends Controller
{
    public function download()
    {
        abort_unless(auth()->user()?->can('manage_backups'), 403);

        $databasePath = database_path('database.sqlite');

        if (! file_exists($databasePath)) {
            abort(404, 'Database file not found.');
        }

        $filename = 'asset-manager-backup-' . now()->format('Y-m-d_H-i-s') . '.sqlite';

        return Response::download($databasePath, $filename);
    }
}