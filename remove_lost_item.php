<?php
session_start();

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);

    // Delete the item
    $stmt = $conn->prepare("DELETE FROM lost_items WHERE id = ?");
    $stmt->bind_param("i", $item_id);

    if ($stmt->execute()) {
        header("Location: admin_dashboard.php?msg=Item+Removed");
        exit();
    } else {
        echo "Error removing item.";
    }

    $stmt->close();
    $conn->close();
}
?>
