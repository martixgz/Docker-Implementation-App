<?php
session_start();
require_once "db.php";

/* Block if not logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id  = (int) $_SESSION['user_id'];
$event_id = (int) $_POST['event_id'];

/* Check if the current user is the creator */
$stmt = $conn->prepare("SELECT creator_id FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event || $event['creator_id'] !== $user_id) {
    // Not allowed
    header("Location: dashboard.php");
    exit;
}

/* Delete event */
$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();

/* Also delete from user_events table */
$stmt = $conn->prepare("DELETE FROM user_events WHERE event_id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();

/* Back to dashboard */
header("Location: dashboard.php");
exit;
