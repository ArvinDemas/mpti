<?php
include_once 'db.php';

if(isset($_SESSION['user_nim'])) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE nim = :nim LIMIT 1");
    $stmt->execute(['nim' => $_SESSION['user_nim']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if($user) {
        unset($user['password']);
        echo json_encode(["status" => "active", "user" => $user]);
    } else {
        session_destroy();
        echo json_encode(["status" => "inactive"]);
    }
} else {
    echo json_encode(["status" => "inactive"]);
}
?>