<?php
require_once 'db.php';
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'mahasiswa') exit;

$uid = $_SESSION['user']['id'];

// 1. Jadwal & Presensi
$schedule = [];
$sql1 = "SELECT c.*, 
        (SELECT COUNT(*) FROM attendance a WHERE a.student_id = $uid AND a.course_id = c.id AND a.date = CURDATE()) as is_present,
        (SELECT timestamp FROM attendance a WHERE a.student_id = $uid AND a.course_id = c.id AND a.date = CURDATE() LIMIT 1) as present_time
        FROM enrollments e 
        JOIN courses c ON e.course_id = c.id 
        WHERE e.student_id = $uid";
$res1 = $conn->query($sql1);
while($row = $res1->fetch_assoc()) { $schedule[] = $row; }

// 2. Tugas
$tasks = [];
$sql2 = "SELECT a.*, c.name as course_name, 
        (SELECT submitted_at FROM submissions s WHERE s.assignment_id = a.id AND s.student_id = $uid LIMIT 1) as submitted_at
        FROM course_assignments a
        JOIN courses c ON a.course_id = c.id
        JOIN enrollments e ON e.course_id = c.id
        WHERE e.student_id = $uid";
$res2 = $conn->query($sql2);
while($row = $res2->fetch_assoc()) { $tasks[] = $row; }

// 3. Sanggah UKT
$appeals = [];
$sql3 = "SELECT * FROM ukt_appeals WHERE student_id = $uid ORDER BY request_date DESC";
$res3 = $conn->query($sql3);
while($row = $res3->fetch_assoc()) { $appeals[] = $row; }

echo json_encode(["status" => "success", "data" => ["schedule" => $schedule, "tasks" => $tasks, "appeals" => $appeals]]);
?>