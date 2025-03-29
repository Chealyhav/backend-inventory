<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Symfony\Component\Process\Process;

class BackupDatabase extends Command
{
    protected $signature = 'backup:database';
    protected $description = 'Backup PostgreSQL database and store it locally';

    public function handle()
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
            $this->error('Backup failed: ' . $process->getErrorOutput());
            return;
        }

        $this->info('Backup successful: ' . $filename);
    }
}
