<?php
session_start();

// Redirect unauthenticated users
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

// Redirect admin users to admin dashboard
$role = $_SESSION['role'] ?? 'user';
if ($role === 'admin') {
    header("Location: admin_dashboard.php");
    exit();
}

$student_id = $_SESSION['student_id'];

include 'includes/db.php';

// Get user's first name
$stmt = $conn->prepare("SELECT firstname FROM users WHERE student_id = ?");
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$firstname = "User";
if ($row = $result->fetch_assoc()) {
    $firstname = $row['firstname'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard - iFound</title>
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


<!-- Main Content -->
<main class="dashboard-content">
    <h2>Welcome to your Dashboard!</h2>
    <p>You can view lost & found reports, submit items, and manage your profile.</p>

    <div style="text-align: center; margin: 20px 0;">
        <a href="lost_item_form.php" class="btn-lost">Submit Lost Item</a>
        <a href="found_item_form.php" class="btn-primary">Submit Found Item</a>
    </div>

    <!-- Recent Lost Items -->
    <h3 style="text-align: center; margin-top: 30px;">Recent Lost Items</h3>
    <div class="items-container">
        <?php
        $sql = "SELECT item_type, other_item_type, description, location_lost, time_lost, date_lost, image_path FROM lost_items WHERE is_approved = 1 ORDER BY date_lost DESC LIMIT 5";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($item = $result->fetch_assoc()) {
                echo '<div class="item-card">';
                echo '<h4>' . htmlspecialchars($item['item_type']) . '</h4>';
                echo '<p><strong>Description:</strong> ' . htmlspecialchars($item['other_item_type']) . ' ' . htmlspecialchars($item['description']) . '</p>';
                echo '<p><strong>Last Located:</strong> ' . htmlspecialchars($item['location_lost']) . '</p>';
                echo '<p><strong>Time Lost:</strong> ' . htmlspecialchars($item['time_lost']) . '</p>';
                echo '<small>Date Lost: ' . htmlspecialchars($item['date_lost']) . '</small>';
                if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                    echo '<div style="margin-top:10px;"><img src="' . htmlspecialchars($item['image_path']) . '" alt="Lost Item Image" style="max-width:100%; border-radius:8px;"></div>';
                }
                echo '</div>';
            }
        } else {
            echo '<p style="text-align:center;">No lost items reported yet.</p>';
        }
        ?>
    </div>

    <!-- Recent Found Items -->
    <h3 style="text-align: center; margin-top: 30px;">Recent Found Items</h3>
    <div class="items-container">
        <?php
        $sql = "SELECT item_type, description, location_found, time_found, date_found, image_path 
            FROM found_items 
            WHERE is_approved = 1 AND archived = 0 
            ORDER BY date_found DESC 
            LIMIT 5";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($item = $result->fetch_assoc()) {
                echo '<div class="item-card">';
                echo '<h4>' . htmlspecialchars($item['item_type']) . '</h4>';

                if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                        echo '<div style="margin-top:10px;"><img src="' . htmlspecialchars($item['image_path']) . '" alt="Found Item Image" style="max-width:100%; border-radius:8px;"></div>';
                    } else {
                        echo '<p><i>Information available to admin only.</i></p>';
                    }
                }
    

                echo '</div>';
            }
        } else {
            echo '<p style="text-align:center;">No found items reported yet.</p>';
        }
        ?>
    </div>
</main>

<script>
function toggleSidebar() {
    document.getElementById("sidebar").classList.toggle("active");
}
    // Feedback alert
    <?php
    $feedbackStatus = $_GET['feedback'] ?? '';
    if ($feedbackStatus === 'exists') {
        echo 'alert("You have already submitted feedback. Only one feedback is allowed per user.");';
    }
    ?>
</script>

</body>
</html>
