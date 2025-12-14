<?php
include_once 'db.php';
$data = json_decode(file_get_contents("php://input"));

if(isset($data->nim) && isset($data->email) && isset($data->tgl_lahir)) {
    $stmt = $conn->prepare("SELECT * FROM users WHERE nim=:nim AND email=:email AND tgl_lahir=:tgl");
    $stmt->execute(['nim' => $data->nim, 'email' => $data->email, 'tgl' => $data->tgl_lahir]);

    if($stmt->rowCount() > 0) {
        $conn->prepare("UPDATE users SET is_active = 1 WHERE nim=:nim")->execute(['nim' => $data->nim]);
        echo json_encode(["status" => "success", "message" => "Akun aktif! Silakan login."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Data tidak cocok."]);
    }
}
?>