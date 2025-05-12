<?php
include('includes/db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $student_id = $_POST['student_id'];
    $mobilenumber = $_POST['mobilenumber'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];
    $recaptchaSecret = '6Le-kTUrAAAAAM_HBWMWbEJtktarPCBtCQNMsw55';
    $recaptchaResponse = $_POST['g-recaptcha-response'];

    // Verify with Google
    $verify = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
    $responseData = json_decode($verify);

if (!$responseData->success) {
    echo "<script>alert('reCAPTCHA verification failed.'); window.history.back();</script>";
    exit();
}


    // Check if the student_id starts with '01'
    if (substr($student_id, 0, 2) !== '01') {
        echo "<script>alert('Invalid student/faculty id. It must start with 01.'); window.history.back();</script>";
        exit(); 
    }
    // Check if the Mobile number starts with '09'
    if (substr($mobilenumber, 0, 2) !== '09') {
        echo "<script>alert('Invalid mobile numer It must start with 09.'); window.history.back();</script>";
        exit(); 
    }

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

    // Check if password length is at least 8 characters and at most 64 characters
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,64}$/', $password)) {
        echo "<script>alert('Password must be 8-64 characters long and include uppercase, lowercase, number, and special character.'); window.history.back();</script>";
        exit();
    }

    // Basic password check
    if ($password !== $confirm) {
        echo "<script>alert('Passwords do not match.');</script>";
        exit();
    }

    // Hashed password for protection
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
