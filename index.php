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

if (isset($_POST['action']) && $_POST['action'] == "register") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $password);

    if ($stmt->execute()) {
        sendResponse(true, "User registered successfully");
    } else {
        sendResponse(false, "Registration failed");
    }
}

if (isset($_POST['action']) && $_POST['action'] == "login") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            sendResponse(true, "Login successful", ["user_id" => $user['id']]);
        } else {
            sendResponse(false, "Invalid password");
        }
    } else {
        sendResponse(false, "User not found");
    }
}
?>