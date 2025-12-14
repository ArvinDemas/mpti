<?php
include 'db.php';
if($_SESSION['user']['role'] != 'dosen') exit;
$uid = $_SESSION['user']['id'];

// Ambil kelas yang diajar
$q = $conn->prepare("SELECT * FROM courses WHERE lecturer_id = :uid");
$q->execute(['uid'=>$uid]);
$courses = $q->fetchAll(PDO::FETCH_ASSOC);

// Ambil Presensi Mahasiswa hari ini
$q2 = $conn->prepare("
    SELECT a.*, u.name as mhs_name, c.name as course_name 
    FROM attendance a 
    JOIN users u ON a.student_id = u.id 
    JOIN courses c ON a.course_id = c.id
    WHERE c.lecturer_id = :uid ORDER BY a.date DESC, a.timestamp DESC
");
$q2->execute(['uid'=>$uid]);
$attendance_log = $q2->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["status"=>"success", "data"=>["courses"=>$courses, "logs"=>$attendance_log]]);
?>