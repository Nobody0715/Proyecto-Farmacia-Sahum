<?php
session_start();
if (!defined('BASE_PATH')) define('BASE_PATH', dirname(__DIR__));
require_once BASE_PATH . '/config/database.php';

class Auth {
    public static function isLoggedIn() { return isset($_SESSION['user_id']); }
    public static function isAdmin() { return isset($_SESSION['rol']) && $_SESSION['rol'] === 'admin'; }
    
    // Nueva función: Permite admin o farmacéutico
    public static function canOperate() { 
        return isset($_SESSION['rol']) && ($_SESSION['rol'] === 'admin' || $_SESSION['rol'] === 'farmaceutico'); 
    }

    public static function requireLogin() { if (!self::isLoggedIn()) { header('Location: ../index.php'); exit; } }
    
    public static function requireAdmin() { 
        self::requireLogin(); 
        if (!self::isAdmin()) { die("❌ Acceso denegado: Solo administradores."); } 
    }

    public static function requireOperator() { 
        self::requireLogin(); 
        if (!self::canOperate()) { die("❌ Acceso denegado: No tienes permisos para esta operación."); } 
    }
}

function isLoggedIn() { return Auth::isLoggedIn(); }
function isAdmin() { return Auth::isAdmin(); }
function canOperate() { return Auth::canOperate(); }
function requireLogin() { Auth::requireLogin(); }
function requireAdmin() { Auth::requireAdmin(); }
function requireOperator() { Auth::requireOperator(); }