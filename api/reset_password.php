<?php
include_once 'db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->email)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email=:email");
    $stmt->execute(['email' => $data->email]);

    if($stmt->rowCount() > 0) {
        echo json_encode(["status" => "success", "message" => "Link reset dikirim ke email."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Email tidak ditemukan."]);
    }
}
?>