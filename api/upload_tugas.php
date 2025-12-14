<?php
include_once 'db.php';
if(!isset($_SESSION['user_nim'])) exit(json_encode(["status" => "error"]));

if(isset($_FILES['file'])) {
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) mkdir($target_dir, 0755, true);

    $filename = time() . "_" . basename($_FILES["file"]["name"]);
    $target_file = $target_dir . $filename;
    
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO assignments (nim, filename, file_path) VALUES (:nim, :fname, :fpath)");
        $stmt->execute(['nim' => $_SESSION['user_nim'], 'fname' => $filename, 'fpath' => $target_file]);
        echo json_encode(["status" => "success", "message" => "File berhasil diupload."]);
    } else {
        echo json_encode(["status" => "error", "message" => "Gagal upload file."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "Tidak ada file."]);
}
?>