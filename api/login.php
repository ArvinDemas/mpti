<?php
require_once 'db.php';

$input = json_decode(file_get_contents("php://input"), true);

if (isset($input['username']) && isset($input['password'])) {
    $u = $conn->real_escape_string($input['username']);
    $p = $input['password'];

    $sql = "SELECT * FROM users WHERE username = '$u'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Cek password hash
        if (password_verify($p, $user['password'])) {
            $_SESSION['user'] = $user;
            unset($user['password']); 
            echo json_encode(["status" => "success", "data" => $user]);
        } else {
            echo json_encode(["status" => "error", "message" => "Password Salah! (Gunakan: password)"]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "Username Tidak Ditemukan!"]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Data tidak lengkap"]);
}
?>