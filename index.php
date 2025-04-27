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

if (isset($_POST['action']) && $_POST['action'] == "create_quiz") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $category_id = $_POST['category_id'];
    $created_by = $_POST['created_by'];

    $stmt = $conn->prepare("INSERT INTO quizzes (title, description, category_id, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii", $title, $description, $category_id, $created_by);

    if ($stmt->execute()) {
        sendResponse(true, "Quiz created successfully");
    } else {
        sendResponse(false, "Quiz creation failed");
    }
}

if (isset($_GET['action']) && $_GET['action'] == "get_quizzes") {
    $result = $conn->query("SELECT * FROM quizzes");
    $quizzes = [];
    while ($row = $result->fetch_assoc()) {
        $quizzes[] = $row;
    }
    sendResponse(true, "Quizzes fetched", $quizzes);
}


if (isset($_POST['action']) && $_POST['action'] == "edit_quiz") {
    $quiz_id = $_POST['quiz_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE quizzes SET title = ?, description = ? WHERE id = ?");
    $stmt->bind_param("ssi", $title, $description, $quiz_id);

    if ($stmt->execute()) {
        sendResponse(true, "Quiz updated successfully");
    } else {
        sendResponse(false, "Quiz update failed");
    }
}

if (isset($_POST['action']) && $_POST['action'] == "delete_quiz") {
    $quiz_id = $_POST['quiz_id'];

    $stmt = $conn->prepare("DELETE FROM quizzes WHERE id = ?");
    $stmt->bind_param("i", $quiz_id);

    if ($stmt->execute()) {
        sendResponse(true, "Quiz deleted successfully");
    } else {
        sendResponse(false, "Quiz deletion failed");
    }
}

if (isset($_POST['action']) && $_POST['action'] == "create_question") {
    $quiz_id = $_POST['quiz_id'];
    $question_text = $_POST['question_text'];

    $stmt = $conn->prepare("INSERT INTO questions (quiz_id, question_text) VALUES (?, ?)");
    $stmt->bind_param("is", $quiz_id, $question_text);

    if ($stmt->execute()) {
        sendResponse(true, "Question created successfully");
    } else {
        sendResponse(false, "Question creation failed");
    }
}


?>