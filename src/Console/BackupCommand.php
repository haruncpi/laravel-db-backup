<?php

namespace Haruncpi\LaravelDbBackup\Console;

use Haruncpi\LaravelDbBackup\DBbackup;
use Illuminate\Console\Command;

class BackupCommand extends Command
{
    const DISK_SUPPORT = ['local', 's3'];

    protected $signature = 'db:backup {disk?}';

    protected $description = 'It will take a backup of your database';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $disk = strtolower($this->argument('disk') ?? 'local');
        if (!in_array($disk, self::DISK_SUPPORT)) {
            $this->error("Sorry! $disk is not support yet!");
            exit;
        }

        new DBbackup(['disk' => $disk]);
        $this->info("Database backup successful");
    }


}
