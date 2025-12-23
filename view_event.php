<?php
session_start();
require_once "db.php";

/* Ensure user is logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id  = (int) $_SESSION['user_id'];

/* Get event_id from GET */
if (!isset($_GET['event_id'])) {
    echo "<p>No event specified.</p>";
    echo '<a href="dashboard.php">Back to Dashboard</a>';
    exit;
}

$event_id = (int) $_GET['event_id'];

/* Fetch the event that is still upcoming */
$stmt = $conn->prepare("SELECT * FROM events WHERE id=? AND event_time >= NOW()");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    echo "<p>This event has expired or does not exist.</p>";
    echo '<a href="dashboard.php">Back to Dashboard</a>';
    exit;
}

/* Check user status */
$status_stmt = $conn->prepare("SELECT status FROM user_events WHERE user_id=? AND event_id=?");
$status_stmt->bind_param("ii", $user_id, $event_id);
$status_stmt->execute();
$res = $status_stmt->get_result();

if ($row = $res->fetch_assoc()) {
    $status = $row['status'];
} else {
    $status = 'pending';
    $insert_stmt = $conn->prepare(
        "INSERT INTO user_events (user_id, event_id, status) VALUES (?, ?, 'pending')"
    );
    $insert_stmt->bind_param("ii", $user_id, $event_id);
    $insert_stmt->execute();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title><?= htmlspecialchars($event['title']) ?></title>
</head>
<body>

<h2><?= htmlspecialchars($event['title']) ?></h2>
<p><?= nl2br(htmlspecialchars($event['content'])) ?></p>
<p><b>Date & Time:</b> <?= $event['event_time'] ?></p>

<?php if ($status === 'pending'): ?>
<form action="update_event.php" method="post">
    <input type="hidden" name="event_id" value="<?= $event_id ?>">
    <button name="action" value="register">Register</button>
    <button name="action" value="neglect">Neglect</button>
</form>
<?php elseif ($status === 'registered'): ?>
<p>✅ Registered</p>
<?php endif; ?>

<a href="dashboard.php">Back to Dashboard</a>

</body>
</html>
