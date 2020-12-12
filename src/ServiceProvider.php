<?php namespace Haruncpi\LaravelDbBackup;

use Haruncpi\LaravelDbBackup\Console\BackupCommand;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {

    }

    public function register()
    {
        $this->commands([BackupCommand::class]);
    }

}