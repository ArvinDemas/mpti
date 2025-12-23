<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// GANTI INI DENGAN KREDENSIAL INFINITYFREE ANDA
$host = "sql105.infinityfree.com"; // Cek di Control Panel
$user = "if0_40677112";       // Username database Anda
$pass = "R9hJiD1m1GGl2";            // Password database Anda
$db   = "if0_40677112_portal_upn";       // Nama database Anda

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "message" => "DB Error: " . $conn->connect_error]));
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>