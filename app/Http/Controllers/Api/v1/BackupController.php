<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Carbon\Carbon;

class BackupController extends Controller
{
    public function createBackup()
    {
        $filename = 'backup_' . Carbon::now()->format('Y-m-d_H-i-s') . '.sql';
        $path = storage_path('app/backups/' . $filename);

        $command = [
            'pg_dump',
            '-U', env('DB_USERNAME'),
            '-h', env('DB_HOST'),
            '-p', env('DB_PORT'),
            '-d', env('DB_DATABASE'),
            '-F', 'c',
            '-f', $path
        ];

        $process = new Process($command);
        $process->setEnv(['PGPASSWORD' => env('DB_PASSWORD')]);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json(['message' => 'Backup failed', 'error' => $process->getErrorOutput()], 500);
        }

        return response()->json(['message' => 'Backup successful', 'filename' => $filename]);
    }

    public function listBackups()
    {
        $files = Storage::files('backups');
        return response()->json($files);
    }
}
