<?php
require "db.php";

$token = $_GET['token'];

$stmt = $conn->prepare(
    "UPDATE users SET verified=1, verify_token=NULL WHERE verify_token=?"
);
$stmt->bind_param("s", $token);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo "Email verified. You can now login.";
} else {
    echo "Invalid or expired verification link.";
}
