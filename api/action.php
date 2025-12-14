<?php
require_once 'db.php';

/** @var mysqli $conn */

$action = $_POST['action'] ?? '';
$nim = $_POST['nim'] ?? '';

if (empty($action) || empty($nim)) {
    echo json_encode(["status" => "error", "message" => "Data Tidak Lengkap"]);
    exit;
}

$nim = $conn->real_escape_string($nim);

if ($action == 'presensi') {
    $id = (int)$_POST['id'];
    $status = $conn->real_escape_string($_POST['status']);
    
    $sql = "INSERT INTO attendance (nim, schedule_id, status) VALUES ('$nim', $id, '$status')";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Presensi Berhasil!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal Presensi: " . $conn->error]);
    }
} 
else if ($action == 'upload') {
    $id = (int)$_POST['id'];
    // Simpan path file dummy (karena di localhost mungkin folder permission ribet)
    $path = "uploads/tugas_" . time() . ".pdf";
    
    $sql = "INSERT INTO submissions (nim, assignment_id, file_path) VALUES ('$nim', $id, '$path')";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Tugas Berhasil Diupload!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal Upload: " . $conn->error]);
    }
} 
else if ($action == 'sanggah') {
    $reason = $conn->real_escape_string($_POST['reason']);
    
    $sql = "INSERT INTO ukt_appeals (nim, reason) VALUES ('$nim', '$reason')";
    if ($conn->query($sql)) {
        echo json_encode(["status" => "success", "message" => "Sanggahan Terkirim!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal Kirim Sanggahan: " . $conn->error]);
    }
}
?>