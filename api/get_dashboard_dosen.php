<?php
require_once 'db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'dosen') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

$uid = $_SESSION['user']['id'];

// 1. Ambil kelas yang diajar
$courses = [];
$sql1 = "SELECT * FROM courses WHERE lecturer_id = $uid";
$result1 = $conn->query($sql1);
if($result1) {
    while($row = $result1->fetch_assoc()) {
        $courses[] = $row;
    }
}

// 2. Ambil Presensi Mahasiswa (log terbaru)
$logs = [];
$sql2 = "SELECT a.*, 
            u.name as mhs_name, 
            c.name as course_name 
        FROM attendance a 
        JOIN users u ON a.student_id = u.id 
        JOIN courses c ON a.course_id = c.id
        WHERE c.lecturer_id = $uid 
        ORDER BY a.date DESC, a.timestamp DESC
        LIMIT 50";

$result2 = $conn->query($sql2);
if($result2) {
    while($row = $result2->fetch_assoc()) {
        $logs[] = $row;
    }
}

echo json_encode([
    "status" => "success", 
    "data" => [
        "courses" => $courses, 
        "logs" => $logs
    ]
]);