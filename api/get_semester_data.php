<?php
include_once 'db.php';
if(!isset($_SESSION['user_nim'])) exit(json_encode(["status" => "error"]));

$nim = $_SESSION['user_nim'];
$query = "SELECT c.id, c.code, c.name, c.sks, c.lecturer FROM enrollments e JOIN courses c ON e.course_id = c.id WHERE e.nim = :nim";
$stmt = $conn->prepare($query);
$stmt->execute(['nim' => $nim]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach($courses as &$course) {
    $mStmt = $conn->prepare("SELECT title, filename, file_path FROM course_materials WHERE course_id = :cid");
    $mStmt->execute(['cid' => $course['id']]);
    $course['materials'] = $mStmt->fetchAll(PDO::FETCH_ASSOC);
}

echo json_encode(["status" => "success", "data" => $courses]);
?>