<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
    public string $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;
    public string $defaultGroup = 'default';

    // --------------------------------------------------------------------------
    // LOCAL XAMPP DEFAULTS (Offline Development)
    // --------------------------------------------------------------------------
    public array $default = [
        'DSN'      => '', 
        'hostname' => '127.0.0.1',
        'username' => 'root',
        'password' => '',
        'database' => 'gad_submission_system',
        'DBDriver' => 'MySQLi',
        'DBPrefix' => '',
        'pConnect' => false,
        'DBDebug'  => true,
        'charset'  => 'utf8mb4',
        'DBCollat' => 'utf8mb4_general_ci',
        'swapPre'  => '',
        'encrypt'  => false,    // LOCAL ENCRYPTION OFF
        'compress' => false,
        'strictOn' => false,
        'failover' => [],
        'port'     => 3306,    
    ];

    // --------------------------------------------------------------------------
    // TESTING DEFAULTS (For automated PHPUnit testing)
    // --------------------------------------------------------------------------
    public array $tests = [
        'DSN'         => '',
        'hostname'    => '127.0.0.1',
        'username'    => '',
        'password'    => '',
        'database'    => ':memory:',
        'DBDriver'    => 'SQLite3',
        'DBPrefix'    => 'db_',
        'pConnect'    => false,
        'DBDebug'     => true,
        'charset'     => 'utf8',
        'DBCollat'    => 'utf8_general_ci',
        'swapPre'     => '',
        'encrypt'     => false,
        'compress'    => false,
        'strictOn'    => false,
        'failover'    => [],
        'port'        => 3306,
        'foreignKeys' => true,
        'busyTimeout' => 1000,
    ];

    public function __construct()
    {
        parent::__construct();

        // --------------------------------------------------------------------------
        // CLOUD OVERRIDE LOGIC (Render Deployment)
        // --------------------------------------------------------------------------
        
        // 1. Check if we are in the cloud by looking for Render's DB_DSN variable
        $dsnString = getenv('DB_DSN') ?: '';

        // 2. If the string exists, we are on Render! Overwrite the local defaults.
        if (!empty($dsnString)) {
            $parsed = parse_url($dsnString);

            if ($parsed) {
                $this->default['hostname'] = $parsed['host'] ?? '';
                $this->default['username'] = $parsed['user'] ?? '';
                $this->default['password'] = $parsed['pass'] ?? '';
                
                // Remove the leading slash from the database name
                $this->default['database'] = ltrim($parsed['path'] ?? '', '/');
                
                // Set the cloud port
                $this->default['port']     = isset($parsed['port']) ? (int) $parsed['port'] : 26685;
                
                // TURN ENCRYPTION BACK ON FOR CLOUD DATABASE!
                $this->default['encrypt']  = ['ssl_verify' => false]; 
            }
        }

        // Standard shorthand environment variables support (DB_HOST, DB_USER, etc.)
        $dbHost = env('database.default.hostname') ?: env('DB_HOST') ?: getenv('DB_HOST');
        if ($dbHost) $this->default['hostname'] = $dbHost;
        $dbUser = env('database.default.username') ?: env('DB_USER') ?: getenv('DB_USER');
        if ($dbUser) $this->default['username'] = $dbUser;
        $dbPass = env('database.default.password') ?? env('DB_PASS') ?? getenv('DB_PASS');
        if ($dbPass !== null && $dbPass !== false) $this->default['password'] = (string)$dbPass;
        $dbName = env('database.default.database') ?: env('DB_NAME') ?: getenv('DB_NAME');
        if ($dbName) $this->default['database'] = $dbName;
        $dbPort = env('database.default.port') ?: env('DB_PORT') ?: getenv('DB_PORT');
        if ($dbPort) $this->default['port'] = (int)$dbPort;
        $dbDriver = env('database.default.DBDriver') ?: env('DB_DRIVER') ?: getenv('DB_DRIVER');
        if ($dbDriver) $this->default['DBDriver'] = $dbDriver;

        // Apply test group if running test suites
        if (ENVIRONMENT === 'testing') {
            $this->defaultGroup = 'tests';
        }
    }
}