<?php
require_once 'db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$id = (int)$_POST['id'];
$status = $_POST['status']; // 'Disetujui' or 'Ditolak'

$status = $conn->real_escape_string($status);

$sql = "UPDATE ukt_appeals SET status = '$status' WHERE id = $id";
if($conn->query($sql)) {
    echo json_encode(["status" => "success", "message" => "Status sanggah berhasil diupdate!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal update: " . $conn->error]);
}