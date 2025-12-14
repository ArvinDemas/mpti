<?php
include_once 'db.php';
session_destroy();
echo json_encode(["status" => "success"]);
?>