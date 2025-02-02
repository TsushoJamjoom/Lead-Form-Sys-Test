<?php

namespace App\Console\Commands;

use App\CentralLogics\Helpers;
use Illuminate\Console\Command;

class DatabaseBackup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'database:backup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creating a database backup in PHP typically involves using the mysqldump command or equivalent for your database system to export the database to a file.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        try {
            // Database configuration
            $host = env('DB_HOST');
            $username = env('DB_USERNAME');
            $password = env('DB_PASSWORD');
            $database = env('DB_DATABASE');

            // Define the path where the backup file will be saved
            $fileName = date('Y-m-d-H-i-s-') . $database . '.sql';
            $backupDir = storage_path('app/public/backup/');

            if (!file_exists($backupDir)) {
                mkdir($backupDir, 0777, true);
            }
            $backupFile = $backupDir . $fileName;
            // Use mysqldump to create a backup
            $command = "mysqldump -h {$host} -u {$username} -p{$password} {$database} > {$backupFile}";

            // Execute the command
            exec($command, $output, $returnCode);
        } catch (\Exception $e) {
            Helpers::errorLog($e);
        }
    }
}
