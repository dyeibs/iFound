<?php
session_start();
if ($_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

include 'includes/db.php';

$item_id = $_POST['item_id'];
$item_type = $_POST['item_type'];
$action = $_POST['action'];

$table = ($item_type === 'lost') ? 'lost_items' : 'found_items';
$is_approved = ($action === 'approve') ? 1 : 0;

$stmt = $conn->prepare("UPDATE $table SET is_approved = ? WHERE id = ?");
$stmt->bind_param("ii", $is_approved, $item_id);
$stmt->execute();

header("Location: admin_dashboard.php");
exit();
