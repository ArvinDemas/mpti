<?php
include_once 'db.php';
if(!isset($_SESSION['user_nim'])) exit(json_encode(["status" => "error"]));

$data = json_decode(file_get_contents("php://input"));

if(isset($data->status) && isset($data->course)) {
    $stmt = $conn->prepare("INSERT INTO attendance_logs (nim, course_name, status) VALUES (:nim, :course, :status)");
    $res = $stmt->execute(['nim' => $_SESSION['user_nim'], 'course' => $data->course, 'status' => $data->status]);
    
    if($res) echo json_encode(["status" => "success", "message" => "Presensi berhasil dicatat."]);
    else echo json_encode(["status" => "error", "message" => "Gagal mencatat presensi."]);
}
?>