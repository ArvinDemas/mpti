<?php
require_once 'db.php';

// Log semua request
error_log("=== LOGIN REQUEST START ===");
error_log("POST data: " . print_r($_POST, true));

$rawInput = file_get_contents("php://input");
error_log("Raw input: " . $rawInput);

$input = json_decode($rawInput, true);
error_log("Decoded JSON: " . print_r($input, true));

if (isset($input['username']) && isset($input['password'])) {
    $u = $conn->real_escape_string($input['username']);
    $p = $input['password'];

    error_log("Attempting login for username: " . $u);

    $sql = "SELECT * FROM users WHERE username = '$u'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        error_log("User found: " . $user['name']);
        error_log("Stored password (first 30 chars): " . substr($user['password'], 0, 30));
        error_log("Input password: " . $p);
        
        // CEK 3 METODE
        // 1. Password Hash Verify
        if (password_verify($p, $user['password'])) {
            error_log("Login SUCCESS via password_verify");
            $_SESSION['user'] = $user;
            unset($user['password']); 
            echo json_encode(["status" => "success", "data" => $user, "method" => "hash"]);
            exit;
        }
        
        // 2. Plain text match (untuk backward compatibility)
        if ($user['password'] === $p) {
            error_log("Login SUCCESS via plain text match");
            // Update ke hash untuk keamanan
            $newHash = password_hash($p, PASSWORD_DEFAULT);
            $conn->query("UPDATE users SET password = '$newHash' WHERE id = {$user['id']}");
            
            $_SESSION['user'] = $user;
            unset($user['password']); 
            echo json_encode(["status" => "success", "data" => $user, "method" => "plain"]);
            exit;
        }
        
        // 3. Cek apakah password yang tersimpan sudah hash atau belum
        if (strlen($user['password']) < 20) {
            // Kemungkinan password belum di-hash, coba direct compare
            if ($user['password'] === $p) {
                error_log("Login SUCCESS via direct compare");
                $_SESSION['user'] = $user;
                unset($user['password']); 
                echo json_encode(["status" => "success", "data" => $user, "method" => "direct"]);
                exit;
            }
        }
        
        error_log("Password mismatch");
        echo json_encode([
            "status" => "error", 
            "message" => "Password Salah! Coba 'password' (huruf kecil)",
            "debug" => [
                "username_input" => $u,
                "password_input" => $p,
                "password_length" => strlen($p),
                "stored_pass_length" => strlen($user['password'])
            ]
        ]);
    } else {
        error_log("User not found: $u");
        echo json_encode([
            "status" => "error", 
            "message" => "Username '$u' Tidak Ditemukan! Jalankan: api/setup.php"
        ]);
    }
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Data tidak lengkap. Username: " . ($input['username'] ?? 'kosong') . ", Pass: " . ($input['password'] ?? 'kosong')
    ]);
}

error_log("=== LOGIN REQUEST END ===");