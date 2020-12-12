<?php namespace Haruncpi\LaravelDbBackup;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;


class DBbackup
{
    private $user;
    private $pass;
    private $dbname;
    private $host;

    private $fileName;
    private $directory;
    private $filePath;
    private $disk;

    const DISK_SUPPORT = ['local', 's3'];


    function __construct($config = [])
    {
        $this->setDbInfo();
        $this->disk = 'local';

        if (array_key_exists('disk', $config)) {
            $this->disk = $config['disk'];
        }

        if (!in_array($this->disk, self::DISK_SUPPORT)) {
            Log::critical("$this->disk is not support yet");
            exit;
        }

        $this->directory = 'backups';
        $this->fileName = 'backup-' . time() . '.sql';
        $this->filePath = $this->directory . "\\" . $this->fileName;

        //make backup
        if (function_exists('shell_exec')) {
            $this->backupByExe();
        } else {
            $this->backupByPdo();
        }
    }

    private function setDbInfo()
    {
        $this->host = Config::get('database.connections.mysql.host');
        $this->user = Config::get('database.connections.mysql.username');
        $this->pass = Config::get('database.connections.mysql.password');
        $this->dbname = Config::get('database.connections.mysql.database');
    }

    private function saveBackupFile($content)
    {
        switch ($this->disk) {
            case 'local':
                Storage::disk('local')->put($this->filePath, $content);
                break;

            case 's3':
                Storage::disk('s3')->put($this->filePath, $content);
                break;
        }
    }

    /**
     * @return bool
     */
    private function backupByExe()
    {
        if (function_exists('shell_exec')) {
            try {
                $storePath = storage_path("app\\$this->filePath");
                if (!is_dir(dirname($storePath))) {
                    mkdir(dirname($storePath), 0755, true);
                }

                $dumpCommand = "mysqldump --user $this->user $this->pass $this->dbname > \"$storePath\"";
                shell_exec($dumpCommand);

                switch ($this->disk) {
                    case 's3':
                        $this->saveBackupFile(file_get_contents($storePath));
                        //now delete from local
                        if (File::exists($storePath)) {
                            File::delete($storePath);
                        }
                        break;
                }

                return true;

            } catch (\Exception $e) {
                Log::critical('DB backup failed');
                Log::info($e->getMessage());
            }
        }
    }

    /**
     * @param string $tables
     * @return bool
     */
    private function backupByPdo($tables = '*')
    {
        $pdo = new \PDO("mysql:host=$this->host;dbname=$this->dbname; charset=utf8", $this->user, $this->pass);

        try {

            // Get all of the tables
            if ($tables == '*') {
                $tables = [];
                $query = $pdo->query('SHOW TABLES');
                while ($row = $query->fetch()) {
                    $tables[] = $row[0];
                }
            } else {
                $tables = is_array($tables) ? $tables : explode(',', $tables);
            }

            if (empty($tables)) {
                return false;
            }

            $out = 'SET sql_mode = "";' . "\n";

            // Loop through the tables
            foreach ($tables as $table) {
                $query = $pdo->query('SELECT * FROM ' . $table);
                $numColumns = $query->columnCount();

                // Add DROP TABLE statement
                $out .= 'DROP TABLE IF EXISTS ' . $table . ';' . "\n\n";

                // Add CREATE TABLE statement
                $query2 = $pdo->query('SHOW CREATE TABLE ' . $table);
                $row2 = $query2->fetch();
                $out .= $row2[1] . ';' . "\n\n";

                // Add INSERT INTO statements
                for ($i = 0; $i < $numColumns; $i++) {
                    while ($row = $query->fetch()) {
                        $out .= "INSERT INTO $table VALUES(";
                        for ($j = 0; $j < $numColumns; $j++) {
                            $row[$j] = addslashes($row[$j]);
                            $row[$j] = preg_replace("/\n/us", "\\n", $row[$j]);
                            if (isset($row[$j])) {
                                $out .= '"' . $row[$j] . '"';
                            } else {
                                $out .= '""';
                            }
                            if ($j < ($numColumns - 1)) {
                                $out .= ',';
                            }
                        }
                        $out .= ');' . "\n";
                    }
                }
                $out .= "\n\n\n";
            }

            // Save file

            $this->saveBackupFile($out);

            $pdo = null;

        } catch (\Exception $e) {
            Log::critical('DB backup failed');
            Log::info($e->getMessage());
            return false;
        }

        return true;
    }

}