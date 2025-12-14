<?php
// FILE: api/fix_user.php
header("Content-Type: text/html");
require_once 'db.php'; // Pastikan file ini ada

echo "<h1>🛠️ Perbaikan Akun Otomatis</h1>";

// 1. CEK KONEKSI DATABASE
if ($conn->connect_error) {
    die("<h3 style='color:red'>❌ KONEKSI DATABASE GAGAL!</h3>
         <p>Pesan Error: " . $conn->connect_error . "</p>
         <p>Solusi: Cek file <b>api/db.php</b>. Pastikan username, password, dan nama database benar.</p>");
}
echo "<p style='color:green'>✅ Koneksi Database: <b>BERHASIL</b></p>";

// 2. CEK TABEL USERS
$checkTable = $conn->query("SHOW TABLES LIKE 'users'");
if ($checkTable->num_rows == 0) {
    die("<h3 style='color:red'>❌ TABEL TIDAK DITEMUKAN!</h3>
         <p>Solusi: Database Anda kosong. Silakan jalankan script SQL reset database lagi di phpMyAdmin.</p>");
}

// 3. RESET AKUN MAHASISWA
$nim = '124230001';
$passAsli = 'password';
$passHash = password_hash($passAsli, PASSWORD_DEFAULT);

// Hapus user jika sudah ada (biar bersih)
$conn->query("DELETE FROM users WHERE username = '$nim'");

// Buat ulang user
$sql = "INSERT INTO users (username, password, name, role, email, major, faculty) 
        VALUES ('$nim', '$passHash', 'Agustinus Wahyu (Fixed)', 'mahasiswa', 'mhs@upn.ac.id', 'Sistem Informasi', 'FTI')";

if ($conn->query($sql)) {
    echo "<div style='border:2px solid green; padding:10px; margin-top:20px;'>";
    echo "<h3 style='color:green'>✅ AKUN BERHASIL DIPERBAIKI!</h3>";
    echo "<p>Silakan login dengan data ini (Jangan Typo):</p>";
    echo "<ul>";
    echo "<li>Username: <b>$nim</b></li>";
    echo "<li>Password: <b>$passAsli</b></li>";
    echo "</ul>";
    echo "<button onclick=\"window.location.href='../index.html'\" style='padding:10px 20px; font-size:16px; cursor:pointer;'>👉 KLIK DISINI UNTUK LOGIN</button>";
    echo "</div>";
} else {
    echo "<h3 style='color:red'>❌ Gagal Membuat User: " . $conn->error . "</h3>";
}
?>