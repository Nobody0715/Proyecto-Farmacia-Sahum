<?php
class Database {
    private static $instance = null;
    public $pdo;

    private function __construct() {
        $host = 'localhost';
        $db   = 'farmacia_sahum';
        $user = 'root';
        $pass = '';

        try {
            $this->pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }
}

$dbInstance = Database::getInstance();
$pdo = $dbInstance->pdo;
?>