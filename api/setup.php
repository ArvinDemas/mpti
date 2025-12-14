<?php
// FILE: api/setup.php - SETUP DATABASE LENGKAP
header("Content-Type: text/html; charset=utf-8");
require_once 'db.php';

echo "<style>body{font-family:Arial;padding:20px;} .success{color:green;} .error{color:red;} .info{background:#f0f0f0;padding:10px;margin:10px 0;border-left:4px solid #333;}</style>";
echo "<h1>🔧 Setup Database Portal UPN</h1>";

// 1. Hapus Tabel Lama
echo "<h2>1️⃣ Menghapus Tabel Lama...</h2>";
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['attendance', 'submissions', 'course_assignments', 'ukt_appeals', 'enrollments', 'courses', 'users'];
foreach($tables as $t) {
    $conn->query("DROP TABLE IF EXISTS $t");
    echo "<p>✓ Tabel <b>$t</b> dihapus</p>";
}
$conn->query("SET FOREIGN_KEY_CHECKS = 1");

// 2. Buat Tabel Baru
echo "<h2>2️⃣ Membuat Struktur Tabel...</h2>";
$sql_create = "
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('mahasiswa', 'dosen', 'admin') NOT NULL,
    email VARCHAR(100),
    major VARCHAR(100),
    faculty VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(10),
    name VARCHAR(100),
    sks INT,
    semester INT,
    lecturer_id INT,
    day VARCHAR(20),
    time_start VARCHAR(10),
    time_end VARCHAR(10),
    room VARCHAR(50),
    FOREIGN KEY (lecturer_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE course_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    title VARCHAR(100),
    deadline DATETIME,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT,
    student_id INT,
    file_path VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES course_assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    status ENUM('Hadir', 'Sakit', 'Izin') DEFAULT 'Hadir',
    date DATE,
    timestamp TIME,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE ukt_appeals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    description TEXT,
    status ENUM('Menunggu', 'Disetujui', 'Ditolak') DEFAULT 'Menunggu',
    request_date DATE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";

if ($conn->multi_query($sql_create)) {
    do {
        if ($result = $conn->store_result()) {
            $result->free();
        }
    } while ($conn->next_result());
    echo "<p class='success'>✅ Semua tabel berhasil dibuat!</p>";
} else {
    die("<p class='error'>❌ Error: " . $conn->error . "</p>");
}

// 3. ISI DATA USER
echo "<h2>3️⃣ Mengisi Data User...</h2>";
$passHash = password_hash("password", PASSWORD_DEFAULT);

$users = [
    ['124230001', $passHash, 'Agustinus Wahyu Wibowo', 'mahasiswa', 'agustinus.w@student.upnyk.ac.id', 'Sistem Informasi', 'Fakultas Teknik Industri'],
    ['19800101', $passHash, 'Dr. Bambang Santoso', 'dosen', 'bambang.s@upnyk.ac.id', 'Sistem Informasi', 'Fakultas Teknik Industri'],
    ['19750515', $passHash, 'Dr. Rina Marlina', 'dosen', 'rina.m@upnyk.ac.id', 'Sistem Informasi', 'Fakultas Teknik Industri'],
    ['admin', $passHash, 'Super Admin', 'admin', 'admin@upnyk.ac.id', '-', '-']
];

foreach($users as $u) {
    $sql = "INSERT INTO users (username, password, name, role, email, major, faculty) 
            VALUES ('{$u[0]}', '{$u[1]}', '{$u[2]}', '{$u[3]}', '{$u[4]}', '{$u[5]}', '{$u[6]}')";
    if($conn->query($sql)) {
        echo "<p>✓ User <b>{$u[2]}</b> ({$u[3]}) dibuat</p>";
    }
}

// 4. Data Mata Kuliah
echo "<h2>4️⃣ Mengisi Data Mata Kuliah...</h2>";
$courses = [
    ['TI-301', 'Rekayasa Perangkat Lunak', 3, 5, 3, 'Senin', '09:30', '11:10', 'R.302'],
    ['TI-302', 'Manajemen Proyek TI', 3, 5, 2, 'Senin', '13:00', '14:40', 'R.101'],
    ['TI-303', 'Pemrograman Mobile', 3, 5, 2, 'Selasa', '07:30', '09:10', 'R.205'],
    ['TI-304', 'Basis Data Lanjut', 3, 5, 3, 'Rabu', '10:00', '11:40', 'R.107']
];

foreach($courses as $c) {
    $sql = "INSERT INTO courses (code, name, sks, semester, lecturer_id, day, time_start, time_end, room) 
            VALUES ('{$c[0]}', '{$c[1]}', {$c[2]}, {$c[3]}, {$c[4]}, '{$c[5]}', '{$c[6]}', '{$c[7]}', '{$c[8]}')";
    if($conn->query($sql)) {
        echo "<p>✓ Matkul <b>{$c[1]}</b> dibuat</p>";
    }
}

// 5. Enrollment Mahasiswa
echo "<h2>5️⃣ Mendaftarkan Mahasiswa ke Mata Kuliah...</h2>";
$enrollments = [[1, 1], [1, 2], [1, 3], [1, 4]]; // Student ID 1 (Agustinus) ambil 4 matkul
foreach($enrollments as $e) {
    $conn->query("INSERT INTO enrollments (student_id, course_id) VALUES ({$e[0]}, {$e[1]})");
}
echo "<p>✓ Mahasiswa terdaftar di 4 mata kuliah</p>";

// 6. Tugas
echo "<h2>6️⃣ Membuat Tugas...</h2>";
$assignments = [
    [1, 'Laporan Analisis Sistem', '2025-12-20 23:59:00'],
    [2, 'Proposal Proyek Akhir', '2025-12-18 23:59:00'],
    [3, 'Aplikasi Android Sederhana', '2025-12-25 23:59:00']
];

foreach($assignments as $a) {
    $sql = "INSERT INTO course_assignments (course_id, title, deadline) VALUES ({$a[0]}, '{$a[1]}', '{$a[2]}')";
    if($conn->query($sql)) {
        echo "<p>✓ Tugas <b>{$a[1]}</b> dibuat</p>";
    }
}

// SELESAI
echo "<div class='info'>";
echo "<h2>✅ SETUP SELESAI!</h2>";
echo "<h3>📋 Data Login:</h3>";
echo "<table border='1' cellpadding='10' style='border-collapse:collapse;'>";
echo "<tr><th>Role</th><th>Username</th><th>Password</th></tr>";
echo "<tr><td>Mahasiswa</td><td><b>124230001</b></td><td><b>password</b></td></tr>";
echo "<tr><td>Dosen 1</td><td><b>19800101</b></td><td><b>password</b></td></tr>";
echo "<tr><td>Dosen 2</td><td><b>19750515</b></td><td><b>password</b></td></tr>";
echo "<tr><td>Admin</td><td><b>admin</b></td><td><b>password</b></td></tr>";
echo "</table>";
echo "<br><a href='../index.html' style='background:#15803d;color:white;padding:15px 30px;text-decoration:none;border-radius:8px;font-weight:bold;display:inline-block;margin-top:20px;'>🚀 LOGIN SEKARANG</a>";
echo "</div>";