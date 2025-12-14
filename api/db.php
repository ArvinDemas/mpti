<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Konfigurasi XAMPP Default
$host = "localhost";
$user = "root";
$pass = "";
$db   = "portal_upn"; // Pastikan sama dengan nama DB di phpMyAdmin

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB Error: " . $conn->connect_error]));
}
// Mulai sesi
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>