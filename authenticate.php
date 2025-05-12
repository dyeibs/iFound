<?php
session_start();
include 'includes/db.php';

$student_id = $_POST['student_id'];
$password = $_POST['password'];

// Check if the student/Faculty ID starts with '01'
if (substr($student_id, 0, 2) !== '01') {
    echo "Invalid student_id.";
    exit(); // Stop the script if the student_id is invalid
}

// Use prepared statement to avoid SQL injection
$sql = "SELECT * FROM users WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    if (password_verify($password, $row['password'])) {
        // Set session variables
        $_SESSION['student_id'] = $row['student_id'];
        $_SESSION['role'] = $row['role']; // Important for admin checks

        header("Location: dashboard.php");
        exit();
    } else {
        echo "Invalid password.";
    }
} else {
    echo "User not found.";
}
?>
