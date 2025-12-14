<?php
require_once 'db.php';

/** @var mysqli $conn */

$nim = isset($_GET['nim']) ? $conn->real_escape_string($_GET['nim']) : '';

if (empty($nim)) {
    echo json_encode(["status" => "error", "message" => "NIM Kosong"]);
    exit;
}

// 1. Ambil Jadwal & Status Presensi
$schedule = [];
$sql1 = "SELECT s.*, a.status as attendance_status 
         FROM schedules s 
         LEFT JOIN attendance a ON s.id = a.schedule_id AND a.nim = '$nim'";
$res1 = $conn->query($sql1);
if ($res1) {
    while ($row = $res1->fetch_assoc()) {
        $schedule[] = $row;
    }
}

// 2. Ambil Tugas & Status Upload
$tasks = [];
$sql2 = "SELECT a.*, s.submitted_at 
         FROM assignments a 
         LEFT JOIN submissions s ON a.id = s.assignment_id AND s.nim = '$nim'";
$res2 = $conn->query($sql2);
if ($res2) {
    while ($row = $res2->fetch_assoc()) {
        $tasks[] = $row;
    }
}

// 3. Ambil History Sanggah UKT
$appeals = [];
$sql3 = "SELECT * FROM ukt_appeals WHERE nim = '$nim' ORDER BY created_at DESC";
$res3 = $conn->query($sql3);
if ($res3) {
    while ($row = $res3->fetch_assoc()) {
        $appeals[] = $row;
    }
}

echo json_encode([
    "status" => "success",
    "schedule" => $schedule,
    "tasks" => $tasks,
    "appeals" => $appeals
]);
?>