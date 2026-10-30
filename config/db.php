<?php
$config = json_decode(file_get_contents(__DIR__ . '/config.json'), true);
define('DB_HOST', $config['DB_HOST']);
define('DB_USER', $config['DB_USER']);
define('DB_PASSWORD', $config['DB_PASSWORD']);
define('DB_NAME', $config['DB_NAME']);
define('DB_API_KEY', $config['DB_API_KEY']);

class DefaultConfig {
    private static ?DefaultConfig $instance = null; 
    public static function getInstance(): DefaultConfig {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance->con;
    }

    private mysqli $con;
    private function __construct() {
        $this->con = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        if ($this->con->connect_error) {
            die("Connection failed: " . $this->con->connect_error);
        }
    }
}