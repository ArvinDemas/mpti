<?php
require_once 'db.php';
$uid = $_SESSION['user']['id'];
$act = $_POST['action'];

if ($act == 'presensi') {
    $cid = (int)$_POST['course_id'];
    $check = $conn->query("SELECT id FROM attendance WHERE student_id=$uid AND course_id=$cid AND date=CURDATE()");
    
    if ($check->num_rows == 0) {
        $conn->query("INSERT INTO attendance (student_id, course_id, date, timestamp) VALUES ($uid, $cid, CURDATE(), CURTIME())");
        echo json_encode(["status" => "success", "message" => "Presensi Berhasil!"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Sudah Presensi Hari Ini!"]);
    }
}

if ($act == 'upload_tugas') {
    $aid = (int)$_POST['assignment_id'];
    if (isset($_FILES['file'])) {
        // Pastikan folder 'uploads' ada di luar folder api (di root project)
        $target_dir = "../uploads/";
        if (!file_exists($target_dir)) mkdir($target_dir, 0777, true);
        
        $fname = time() . "_" . basename($_FILES['file']['name']);
        $target_file = $target_dir . $fname;
        
        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            // Hapus submisi lama jika ada
            $conn->query("DELETE FROM submissions WHERE student_id=$uid AND assignment_id=$aid");
            // Insert baru
            $sql = "INSERT INTO submissions (student_id, assignment_id, file_path) VALUES ($uid, $aid, '$target_file')";
            $conn->query($sql);
            echo json_encode(["status" => "success", "message" => "Tugas Terkirim!"]);
        } else {
            echo json_encode(["status" => "error", "message" => "Gagal Upload File!"]);
        }
    }
}

if ($act == 'sanggah_ukt') {
    $desc = $conn->real_escape_string($_POST['desc']);
    $conn->query("INSERT INTO ukt_appeals (student_id, description, request_date) VALUES ($uid, '$desc', CURDATE())");
    echo json_encode(["status" => "success", "message" => "Sanggahan Terkirim!"]);
}
?>