<?php
session_start();
include 'includes/db.php';

$student_id = $_POST['student_id'];
$password = $_POST['password'];

$sql = "SELECT * FROM users WHERE student_id='$student_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if (password_verify($password, $row['password'])) {
        $_SESSION['student_id'] = $student_id;
        header("Location: dashboard.php");
    } else {
        echo "Invalid password.";
    }
} else {
    echo "User not found.";
}
?>
