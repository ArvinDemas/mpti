<?php
include_once 'db.php';
if(!isset($_SESSION['user_nim'])) exit(json_encode(["status" => "error", "message" => "Unauthorized"]));

$nim = $_SESSION['user_nim'];

$stmtUKT = $conn->prepare("SELECT * FROM ukt_history WHERE nim = :nim ORDER BY semester DESC");
$stmtUKT->execute(['nim' => $nim]);
$ukt = $stmtUKT->fetchAll(PDO::FETCH_ASSOC);

$stmtSadewa = $conn->prepare("SELECT * FROM sadewa_requests WHERE nim = :nim ORDER BY request_date DESC");
$stmtSadewa->execute(['nim' => $nim]);
$sadewa = $stmtSadewa->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["status" => "success", "data" => ["ukt" => $ukt, "sadewa" => $sadewa]]);
?>