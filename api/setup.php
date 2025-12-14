<?php
// FILE: api/setup.php
header("Content-Type: text/html");
require_once 'db.php';

// 1. Hapus Tabel Lama
$conn->query("SET FOREIGN_KEY_CHECKS = 0");
$tables = ['attendance', 'submissions', 'course_assignments', 'ukt_appeals', 'enrollments', 'courses', 'users'];
foreach($tables as $t) {
    $conn->query("DROP TABLE IF EXISTS $t");
}

// 2. Buat Tabel Baru
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
);

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
);

CREATE TABLE enrollments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE course_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_id INT,
    title VARCHAR(100),
    deadline DATETIME,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE submissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    assignment_id INT,
    student_id INT,
    file_path VARCHAR(255),
    submitted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assignment_id) REFERENCES course_assignments(id) ON DELETE CASCADE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);

CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    course_id INT,
    status ENUM('Hadir', 'Sakit', 'Izin') DEFAULT 'Hadir',
    date DATE,
    timestamp TIME,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE
);

CREATE TABLE ukt_appeals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    description TEXT,
    status ENUM('Menunggu', 'Disetujui', 'Ditolak') DEFAULT 'Menunggu',
    request_date DATE,
    FOREIGN KEY (student_id) REFERENCES users(id) ON DELETE CASCADE
);
";

// Eksekusi Pembuatan Tabel
$conn->multi_query($sql_create);
while ($conn->next_result()) {;} // Flush multi_queries

// 3. ISI DATA DUMMY (DENGAN PASSWORD YANG VALID)
// Password: 'password'
$passHash = password_hash("password", PASSWORD_DEFAULT);

// User Mahasiswa
$sql_ins_user = "INSERT INTO users (username, password, name, role, email, major, faculty) VALUES 
('124230001', '$passHash', 'Agustinus Wahyu Wibowo', 'mahasiswa', 'mhs@upn.ac.id', 'Sistem Informasi', 'FTI'),
('19800101', '$passHash', 'Dr. Bambang Santoso', 'dosen', 'dosen@upn.ac.id', 'Sistem Informasi', 'FTI'),
('admin', '$passHash', 'Super Admin', 'admin', 'admin@upn.ac.id', '-', '-')";
$conn->query($sql_ins_user);

// Data Pendukung
$conn->query("INSERT INTO courses (code, name, sks, semester, lecturer_id, day, time_start, time_end, room) VALUES ('TI-301', 'Rekayasa Perangkat Lunak', 3, 5, 2, 'Senin', '09:30', '11:10', 'R.302'), ('TI-302', 'Manajemen Proyek TI', 3, 5, 2, 'Senin', '13:00', '14:40', 'R.101')");
$conn->query("INSERT INTO enrollments (student_id, course_id) VALUES (1, 1), (1, 2)");
$conn->query("INSERT INTO course_assignments (course_id, title, deadline) VALUES (1, 'Laporan Analisis', '2025-12-31 23:59:00')");

$conn->query("SET FOREIGN_KEY_CHECKS = 1");

echo "<h1>✅ SETUP BERHASIL!</h1>";
echo "<p>Database sudah di-reset. User login telah dibuat:</p>";
echo "<ul><li>Mahasiswa: <b>124230001</b></li><li>Dosen: <b>19800101</b></li><li>Admin: <b>admin</b></li></ul>";
echo "<p>Password untuk semua akun: <b>password</b></p>";
echo "<br><a href='../index.html'>👉 KLIK DISINI UNTUK LOGIN</a>";
?>