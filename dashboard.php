<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}

$user_id = (int) $_SESSION['user_id'];

// Fetch upcoming events
$events = $conn->query("SELECT * FROM events WHERE event_time >= NOW() ORDER BY event_time ASC");

// Prepare statement to check user status
$status_stmt = $conn->prepare(
    "SELECT status FROM user_events WHERE user_id=? AND event_id=?"
);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Events Dashboard</title>
</head>
<body>

<h2>Events Dashboard</h2>

<!-- Logout -->
<form action="logout.php" method="post" style="margin-bottom:20px;">
    <button type="submit">Logout</button>
</form>

<!-- Add new event -->
<form action="add_event.php" method="get" style="margin-bottom:20px;">
    <button type="submit">Add Event</button>
</form>

<ul>
<?php while ($event = $events->fetch_assoc()):
    $event_id = $event['id'];

    // Check user status for this event
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

    // Skip neglected events
    if ($status == 'neglected') continue;
?>

<li>
    <b><?= htmlspecialchars($event['title']) ?></b>
    <?php if ($status == 'registered'): ?>
        ✅ <?= $event['event_time'] ?>
    <?php endif; ?>

    <!-- View Event button -->
    <form action="view_event.php" method="get" style="display:inline;">
        <input type="hidden" name="event_id" value="<?= $event_id ?>">
        <button type="submit">View Event</button>
    </form>

    <!-- Delete Event button for creator only -->
    <?php if ($event['creator_id'] == $user_id): ?>
        <form action="delete_event.php" method="post" style="display:inline;">
            <input type="hidden" name="event_id" value="<?= $event_id ?>">
            <button type="submit" onclick="return confirm('Are you sure you want to delete this event?');">Delete Event</button>
        </form>
    <?php endif; ?>
</li>

<?php endwhile; ?>
</ul>

</body>
</html>
