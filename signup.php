<?php
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $student_id = $_POST['student_id'];
    $mobilenumber = $_POST['mobilenumber'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Check if student_id already exists
    $check = $conn->prepare("SELECT student_id FROM users WHERE student_id = ?");
    $check->bind_param("s", $student_id);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('Student ID already exists. Please use a different one.'); window.history.back();</script>";
        exit();
    }
    $check->close();


    // Basic password check
    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match.');</script>";
        exit();
    }

    // Hashed password para protected
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (firstname, lastname, student_id, mobilenumber, password) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $firstname, $lastname, $student_id, $mobilenumber, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Registration successful!'); window.location='login.php';</script>";
    } else {
        echo "<script>alert('Error: Student ID already registered or other issue.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>
