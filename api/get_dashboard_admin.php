<?php
include 'db.php';
if($_SESSION['user']['role'] != 'admin') exit;

$q = $conn->prepare("SELECT a.*, u.name, u.username as nim FROM ukt_appeals a JOIN users u ON a.student_id = u.id ORDER BY status DESC");
$q->execute();
$appeals = $q->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["status"=>"success", "data"=>["appeals"=>$appeals]]);
?>