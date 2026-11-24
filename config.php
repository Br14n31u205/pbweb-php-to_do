<?php
const DB_HOST = 'localhost';
const DB_NAME = 'db_todo';
const DB_USER = 'root';   
const DB_PASS = '';      


const NAMA = 'Valentinus Nathanael Febrian Elu';
const NIM  = '255314158'; 

session_start();

// Koneksi database (PDO)
function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

// Penjaga halaman: kalau belum login, lempar ke login.php
function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

// Escape output supaya aman dari XSS
function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}
