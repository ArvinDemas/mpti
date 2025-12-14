<?php
include_once 'db.php';
if(!isset($_SESSION['user_nim'])) exit(json_encode(["status" => "error"]));

$nim = $_SESSION['user_nim'];
$query = "SELECT c.* FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.nim = :nim";
$stmt = $conn->prepare($query);
$stmt->execute(['nim' => $nim]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode(["status" => "success", "data" => $courses]);
?>