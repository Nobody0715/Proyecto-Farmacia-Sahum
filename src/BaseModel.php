<?php
namespace App;
abstract class BaseModel {
    protected $db;
    public function __construct(\PDO $pdo) {
        $this->db = $pdo;
    }
}