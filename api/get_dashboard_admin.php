<?php
require_once 'db.php';

if(!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin') {
    echo json_encode(["status" => "error", "message" => "Unauthorized"]);
    exit;
}

// Ambil semua sanggah UKT
$appeals = [];
$sql = "SELECT a.*, u.name, u.username as nim 
        FROM ukt_appeals a 
        JOIN users u ON a.student_id = u.id 
        ORDER BY 
            CASE a.status 
                WHEN 'Menunggu' THEN 1 
                WHEN 'Disetujui' THEN 2 
                WHEN 'Ditolak' THEN 3 
            END,
            a.request_date DESC";

$result = $conn->query($sql);
if($result) {
    while($row = $result->fetch_assoc()) {
        $appeals[] = $row;
    }
}

echo json_encode([
    "status" => "success", 
    "data" => ["appeals" => $appeals]
]);