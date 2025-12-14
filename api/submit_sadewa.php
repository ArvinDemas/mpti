<?php
include_once 'db.php';
if(!isset($_SESSION['user_nim'])) exit(json_encode(["status" => "error"]));

$data = json_decode(file_get_contents("php://input"));

if(isset($data->type) && isset($data->description)) {
    $stmt = $conn->prepare("INSERT INTO sadewa_requests (nim, type, description, status, request_date) VALUES (:nim, :type, :desc, 'Menunggu', CURDATE())");
    $res = $stmt->execute(['nim' => $_SESSION['user_nim'], 'type' => $data->type, 'desc' => $data->description]);
    
    if($res) echo json_encode(["status" => "success", "message" => "Pengajuan berhasil disimpan."]);
    else echo json_encode(["status" => "error", "message" => "Gagal menyimpan."]);
}
?>