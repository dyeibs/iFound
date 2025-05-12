<?php
session_start();
include 'includes/db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_SESSION['student_id'])) {
    $student_id = $_SESSION['student_id'];
    $rating = intval($_POST['rating']);
    $comment = substr(trim($_POST['comment']), 0, 200); // Enforce max length

    // Determine redirection based on role
    $redirect_page = ($_SESSION['role'] === 'admin') ? 'admin_dashboard.php' : 'dashboard.php';

    // Check if feedback already exists for this student_id
    $check_stmt = $conn->prepare("SELECT id FROM feedback WHERE student_id = ?");
    $check_stmt->bind_param("s", $student_id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        header("Location: $redirect_page?feedback=exists");
        exit();
    }

    $check_stmt->close();

    // Insert new feedback
    $stmt = $conn->prepare("INSERT INTO feedback (student_id, rating, comment) VALUES (?, ?, ?)");
    $stmt->bind_param("sis", $student_id, $rating, $comment);
    $stmt->execute();
    $stmt->close();

    header("Location: $redirect_page?feedback=success");
    exit();
}
?>
