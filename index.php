<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "quiz_app";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function sendResponse($status, $message, $data = []) {
    header('Content-Type: application/json');
    echo json_encode(["status" => $status, "message" => $message, "data" => $data]);
    exit;
}


?>