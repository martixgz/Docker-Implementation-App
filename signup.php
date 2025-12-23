<?php
require "db.php";

$email = $_POST['email'];
$token = bin2hex(random_bytes(32));

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email=?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Email already registered. Please login.");
}

// Insert unverified user
$stmt = $conn->prepare(
    "INSERT INTO users (email, verify_token, verified) VALUES (?, ?, 0)"
);
$stmt->bind_param("ss", $email, $token);
$stmt->execute();

$verify_link = "https://unrippled-melida-skidproof.ngrok-free.dev/verify.php?token=$token";

// Send email (TEMP – works only if mail is configured)
$subject = "Verify your account";
$message = "Click to verify:\n$verify_link";
$headers = "From: no-reply@app.com";

mail($email, $subject, $message, $headers);

echo "Verification email sent. Check your inbox.";
