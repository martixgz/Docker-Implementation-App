<?php
session_start();
include 'db.php';

/* Validate input */
if (!isset($_POST['username']) || trim($_POST['username']) === '') {
    die("Username is required");
}

$username = trim($_POST['username']);

/* Check if user exists */
$stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    /* Create user */
    $stmt = $conn->prepare("INSERT INTO users (username) VALUES (?)");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user_id = (int)$stmt->insert_id;
} else {
    $row = $res->fetch_assoc();
    $user_id = (int)$row['id'];
}

/* Store session values */
$_SESSION['user_id'] = $user_id;   // INTEGER
$_SESSION['username'] = $username; // STRING

/* Redirect to dashboard */
header("Location: dashboard.php");
exit;
