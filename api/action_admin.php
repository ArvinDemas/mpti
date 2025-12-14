<?php
include 'db.php';
if($_SESSION['user']['role'] != 'admin') exit;

$id = $_POST['id'];
$status = $_POST['status']; // 'Disetujui' or 'Ditolak'

$stmt = $conn->prepare("UPDATE ukt_appeals SET status = :st WHERE id = :id");
$stmt->execute(['st'=>$status, 'id'=>$id]);

echo json_encode(["status"=>"success", "message"=>"Status diupdate!"]);
?>