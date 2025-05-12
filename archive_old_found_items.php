<?php
session_start();
include 'includes/db.php';

// Session variables
$student_id = $_SESSION['student_id'] ?? null;
$role = $_SESSION['role'] ?? null;

// Automatically archive items older than 90 days
$ninety_days_ago = date('Y-m-d', strtotime('-90 days'));
$stmt = $conn->prepare("UPDATE found_items SET archived = 1 WHERE date_found < ? AND is_approved = 1 AND archived = 0");
$stmt->bind_param("s", $ninety_days_ago);
$stmt->execute();
$archived_count = $stmt->affected_rows;
$stmt->close();

// Manually archive a specific item if submitted via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'])) {
    $item_id = intval($_POST['item_id']);
    $stmt = $conn->prepare("UPDATE found_items SET archived = 1 WHERE id = ?");
    $stmt->bind_param("i", $item_id);
    $stmt->execute();
    $stmt->close();

    // Redirect after manual archiving
    header("Location: admin_dashboard.php?archived=manual");
    exit();
}

// Get user's first name
$firstname = "User";
if ($student_id) {
    $stmt = $conn->prepare("SELECT firstname FROM users WHERE student_id = ?");
    $stmt->bind_param("s", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $firstname = $row['firstname'];
    }
    $stmt->close();
}

// Get archived found items
$archived_items = [];
$query = "SELECT * FROM found_items WHERE archived = 1 AND is_approved = 1 ORDER BY date_found DESC";
$result = $conn->query($query);
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $archived_items[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Found Items - iFound</title>
    <link rel="stylesheet" href="style.css?v=1.0">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="SPCF Logo.png" alt="SPCF Logo">
        <h1>iFound.</h1>
    </div>
    <div class="menu-icon" onclick="toggleSidebar()">
        <i class="fas fa-bars"></i>
    </div>
</header>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="close-btn" onclick="toggleSidebar()">&times;</div>
    <p>Welcome, <?php echo htmlspecialchars($firstname); ?></p>
    <ul class="sidebar-menu">
        <li><a href="profile.php"><i class="fa-solid fa-user"></i> View Profile</a></li>
        <li><a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> Admin Dashboard</a></li>
        <li><a href="lost_item_form.php"><i class="fa-solid fa-plus"></i> Submit Lost Item</a></li>
        <li><a href="found_item_form.php"><i class="fa-solid fa-plus"></i> Submit Found Item</a></li>
        <li><a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></li>
    </ul>

    <!-- Feedback Section -->
    <div class="feedback-section">
        <p>Rate your experience:</p>
        <form action="submit_feedback.php" method="POST" style="display: flex; flex-direction: column; gap: 8px;">
            <select name="rating" required style="padding: 6px; border-radius: 6px;">
                <option value="">Select Rating</option>
                <option value="5">⭐⭐⭐⭐⭐ - Excellent</option>
                <option value="4">⭐⭐⭐⭐ - Good</option>
                <option value="3">⭐⭐⭐ - Average</option>
                <option value="2">⭐⭐ - Poor</option>
                <option value="1">⭐ - Terrible</option>
            </select>
            <textarea name="comment" placeholder="Your feedback..." rows="3" maxlength="200" style="padding: 6px; border-radius: 6px;"></textarea>
            <button type="submit" class="btn-approve" style="padding: 10px; border: none;">Submit</button>
        </form>
    </div>
</div>

<main class="dashboard-content">
    <h2>Welcome to the Archive!</h2>
    <p>You can review archived found reports older than 90 days here.</p>

    <div style="text-align: center; margin: 20px 0;">
        <a href="admin_dashboard.php" class="btn-primary">Back to Admin Dashboard?</a>
    </div>

    <!-- Archived Found Items Section -->
    <h3 style="text-align: center; margin-top: 30px;">Archived Found Items</h3>
    <div class="items-container" style="margin-top: 30px;">
        <?php if (count($archived_items) > 0): ?>
            <?php foreach ($archived_items as $item): ?>
                <div class="item-card">
                    <h4><?php echo htmlspecialchars($item['item_type']); ?></h4>
                    <p><strong>Student ID:</strong> <?php echo htmlspecialchars($item['student_id']); ?></p>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
                    <p><strong>Location Found:</strong> <?php echo htmlspecialchars($item['location_found']); ?></p>
                    <p><strong>Time:</strong> <?php echo htmlspecialchars($item['time_found']); ?></p>
                    <small>Date Found: <?php echo htmlspecialchars($item['date_found']); ?></small>

                    <?php if (!empty($item['image_path']) && file_exists($item['image_path']) && $role === 'admin'): ?>
                        <div style="margin-top:10px;">
                            <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="Found Item Image" style="max-width:100%; border-radius:8px;">
                        </div>
                    <?php endif; ?>

                    <?php if ($role === 'admin'): ?>
                        <?php
                            $qr_data = urlencode("Item Type: {$item['item_type']}\nDescription: {$item['description']}\nLocation Found: {$item['location_found']}");
                            $qr_url = "https://api.qrserver.com/v1/create-qr-code/?data={$qr_data}&size=150x150";
                        ?>
                        <div style="margin-top:10px;">
                            <img src="<?php echo $qr_url; ?>" alt="QR Code for Item">
                            <p><small>QR Code for this found item (admin only)</small></p>
                        </div>

                        <!-- Remove Button -->
                        <form method="POST" action="remove_found_item.php" onsubmit="return confirm('Are you sure you want to remove this item?');" style="margin-top: 10px;">
                            <input type="hidden" name="item_id" value="<?php echo $item['id']; ?>">
                            <button type="submit" class="btn-deny">Remove</button>
                        </form>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p style="text-align: center;">No archived found items to display.</p>
        <?php endif; ?>
    </div>
</main>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
}
</script>

</body>
</html>
