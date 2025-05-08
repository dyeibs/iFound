<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

$student_id = $_SESSION['student_id'];

// Sanitize inputs
$item_type = trim($_POST['item_type']);
$description = trim($_POST['description']);
$location_lost = trim($_POST['location_lost']);
$date_lost = $_POST['date_lost'];
$time_lost = $_POST['time_lost'];

// Handle "Others" item type
if ($item_type === "Others" && !empty($_POST['other_item_type'])) {
    $item_type = trim($_POST['other_item_type']);
}

// Handle image upload
$image_path = null;
if (isset($_FILES['item_image']) && $_FILES['item_image']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create folder if it doesn't exist
    }

    $filename = basename($_FILES['item_image']['name']);
    $target_file = $upload_dir . time() . "_" . $filename;
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
    if (in_array($image_file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES['item_image']['tmp_name'], $target_file)) {
            $image_path = $target_file;
        }
    }
}

// Insert into database
$sql = "INSERT INTO lost_items (student_id, item_type, description, location_lost, date_lost, time_lost, image_path)
        VALUES (?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sssssss", $student_id, $item_type, $description, $location_lost, $date_lost, $time_lost, $image_path);

if ($stmt->execute()) {
    header("Location: dashboard.php?success=1");
    exit();
} else {
    echo "Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
