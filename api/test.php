<?php
header("Content-Type: text/html; charset=utf-8");
echo "<style>body{font-family:monospace;padding:20px;} .ok{color:green;} .err{color:red;} pre{background:#f5f5f5;padding:10px;border-left:3px solid #333;}</style>";
echo "<h1>🔍 Diagnostik Portal UPN</h1><hr>";

// 1. CEK PHP VERSION
echo "<h3>1. PHP Version</h3>";
echo "<p class='ok'>✅ PHP " . phpversion() . "</p>";

// 2. CEK FILE DB.PHP
echo "<h3>2. File db.php</h3>";
if(file_exists('db.php')) {
    echo "<p class='ok'>✅ File db.php ditemukan</p>";
    require_once 'db.php';
} else {
    die("<p class='err'>❌ File db.php TIDAK DITEMUKAN!</p>");
}

// 3. CEK KONEKSI DATABASE
echo "<h3>3. Koneksi Database</h3>";
if ($conn->connect_error) {
    die("<p class='err'>❌ Koneksi GAGAL: " . $conn->connect_error . "</p>");
} else {
    echo "<p class='ok'>✅ Koneksi ke database BERHASIL!</p>";
    echo "<p>Host: localhost<br>Database: portal_upn</p>";
}

// 4. CEK TABEL USERS
echo "<h3>4. Tabel Users</h3>";
$checkTable = $conn->query("SHOW TABLES LIKE 'users'");
if ($checkTable->num_rows == 0) {
    echo "<p class='err'>❌ Tabel 'users' TIDAK DITEMUKAN!</p>";
    echo "<p>Solusi: Jalankan <a href='setup.php' style='background:green;color:white;padding:5px 10px;text-decoration:none;'>setup.php</a></p>";
} else {
    echo "<p class='ok'>✅ Tabel 'users' ditemukan</p>";
    
    // 5. CEK DATA USER
    echo "<h3>5. Data User Testing</h3>";
    $result = $conn->query("SELECT username, name, role, LEFT(password, 30) as pass_preview FROM users");
    
    if($result->num_rows > 0) {
        echo "<p class='ok'>✅ Ditemukan " . $result->num_rows . " user</p>";
        echo "<table border='1' cellpadding='8' style='border-collapse:collapse;'>";
        echo "<tr><th>Username</th><th>Nama</th><th>Role</th><th>Password Preview</th></tr>";
        
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td><b>" . $row['username'] . "</b></td>";
            echo "<td>" . $row['name'] . "</td>";
            echo "<td>" . $row['role'] . "</td>";
            echo "<td><code>" . $row['pass_preview'] . "...</code></td>";
            echo "</tr>";
        }
        echo "</table>";
        
        // 6. TEST LOGIN MANUAL
        echo "<h3>6. Test Login Manual</h3>";
        echo "<p>Mencoba login dengan username: <b>124230001</b>, password: <b>password</b></p>";
        
        $testUser = '124230001';
        $testPass = 'password';
        
        $testResult = $conn->query("SELECT * FROM users WHERE username = '$testUser'");
        if($testResult->num_rows > 0) {
            $user = $testResult->fetch_assoc();
            echo "<p>✓ User ditemukan: " . $user['name'] . "</p>";
            
            // Test password verify
            if(password_verify($testPass, $user['password'])) {
                echo "<p class='ok'>✅ PASSWORD COCOK! (via password_verify)</p>";
            } elseif($user['password'] === $testPass) {
                echo "<p class='ok'>✅ PASSWORD COCOK! (plain text match)</p>";
                echo "<p style='color:orange;'>⚠️ Warning: Password belum di-hash. Akan di-update otomatis saat login.</p>";
            } else {
                echo "<p class='err'>❌ PASSWORD TIDAK COCOK!</p>";
                echo "<p>Password di database: <code>" . substr($user['password'], 0, 50) . "</code></p>";
                echo "<p>Password yang dicoba: <code>$testPass</code></p>";
            }
        } else {
            echo "<p class='err'>❌ User tidak ditemukan</p>";
        }
        
    } else {
        echo "<p class='err'>❌ Tidak ada user di database!</p>";
        echo "<p>Solusi: Jalankan <a href='setup.php' style='background:green;color:white;padding:5px 10px;text-decoration:none;'>setup.php</a></p>";
    }
}

// 7. KESIMPULAN
echo "<hr><h3>📊 Kesimpulan</h3>";
echo "<p>Jika semua checklist ✅ hijau, maka sistem siap digunakan.</p>";
echo "<p>Login di: <a href='../index.html'><b>index.html</b></a></p>";
echo "<p>Username: <b>124230001</b> | Password: <b>password</b></p>";