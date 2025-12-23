<?php
session_start();

/* 🔐 Block access if not logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Event</title>
</head>
<body>

<!-- Back to Dashboard -->
<form action="dashboard.php" method="get">
    <button type="submit">Back to Dashboard</button>
</form>

<h2>Add New Event</h2>

<form method="post" action="save_event.php">

    <label>Event Title:</label><br>
    <input type="text" name="title" required><br><br>

    <label>Event Content:</label><br>
    <textarea name="content" rows="5" cols="40" required></textarea><br><br>

    <label>Event Date & Time:</label><br>
    <input type="datetime-local" name="event_time" required><br><br>

    <button type="submit">Save Event</button>
</form>

</body>
</html>
