<?php
session_start();
require_once "db.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id  = (int) $_SESSION['user_id'];
$event_id = (int) $_POST['event_id'];
$action   = $_POST['action'];

/* Ensure event exists */
$stmt = $conn->prepare("SELECT id FROM events WHERE id=? AND event_time >= NOW()");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    header("Location: dashboard.php");
    exit;
}

/* Update user status */
if ($action === "register") {
    $stmt = $conn->prepare("UPDATE user_events SET status='registered' WHERE user_id=? AND event_id=?");
} else { // neglect
    $stmt = $conn->prepare("UPDATE user_events SET status='neglected' WHERE user_id=? AND event_id=?");
}

$stmt->bind_param("ii", $user_id, $event_id);
$stmt->execute();

header("Location: dashboard.php");
exit;
