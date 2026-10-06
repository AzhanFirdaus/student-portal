<?php

require 'db.php';

$username = "admin";
$password = "admin123";
$full_name = "System Administrator";

$hashed_password = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare(
    "INSERT INTO admins (username, password, full_name)
     VALUES (?, ?, ?)"
);

$stmt->bind_param(
    "sss",
    $username,
    $hashed_password,
    $full_name
);

if ($stmt->execute()) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();

?>