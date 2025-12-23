<?php
session_start();
require_once "db.php";

/* Block if not logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int) $_SESSION['user_id']; // creator_id

$title      = $_POST['title'];
$content    = $_POST['content'];
$event_time = str_replace("T", " ", $_POST['event_time']); // HTML -> MySQL datetime

/* Insert event with creator_id */
$stmt = $conn->prepare(
    "INSERT INTO events (title, content, event_time, creator_id) VALUES (?, ?, ?, ?)"
);
$stmt->bind_param("sssi", $title, $content, $event_time, $user_id);
$stmt->execute();

/* Back to dashboard */
header("Location: dashboard.php");
exit;
