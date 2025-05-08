<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];
include 'includes/db.php';

$sql = "SELECT firstname FROM users WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

$firstname = "User";
if ($result && $row = $result->fetch_assoc()) {
    $firstname = $row['firstname'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - iFound</title>
    <link rel="stylesheet" href="style.css">
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
    <a href="profile.php"><i class="fas fa-user"></i> View Profile</a>
    <a href="lost_item_form.php"><i class="fas fa-plus"></i> Submit Lost Item</a>
    <a href="found_item_form.php"><i class="fas fa-plus-circle"></i> Submit Found Item</a>
    <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Dashboard Content -->
<main class="dashboard-content">
    <h2>Welcome to your Dashboard!</h2>
    <p>Here you can view lost & found reports, submit items, and manage your profile.</p>

    <div style="text-align: center; margin: 20px 0;">
        <a href="lost_item_form.php" class="btn-lost">Submit Lost Item</a>
        <a href="found_item_form.php" class="btn-primary">Submit Found Item</a>
    </div>

    <!-- Lost Items -->
    <h3 style="text-align: center; margin-top: 30px;">Recent Lost Items</h3>
    <div class="items-container">
    <?php
    $sql_items = "SELECT item_type, other_item_type, description, location_lost, time_lost, date_lost, image_path FROM lost_items ORDER BY date_lost DESC LIMIT 5";
    $result_items = $conn->query($sql_items);

    if ($result_items && $result_items->num_rows > 0) {
        while ($item = $result_items->fetch_assoc()) {
            echo '<div class="item-card">';
            echo '<h4>' . htmlspecialchars($item['item_type']) . '</h4>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($item['other_item_type']) . ' ' .  htmlspecialchars($item['description']) . '</p>';
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

    <!-- Found Items -->
    <h3 style="text-align: center; margin-top: 30px;">Recent Found Items</h3>
    <div class="items-container">
    <?php
    $sql_found = "SELECT item_type, description, location_found, time_found, date_found, image_path FROM found_items ORDER BY date_found DESC LIMIT 5";
    $result_found = $conn->query($sql_found);

    if ($result_found && $result_found->num_rows > 0) {
        while ($item = $result_found->fetch_assoc()) {
            echo '<div class="item-card">';
            echo '<h4>' . htmlspecialchars($item['item_type']) . '</h4>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($item['description']) . '</p>';
            echo '<p><strong>Found At:</strong> ' . htmlspecialchars($item['location_found']) . '</p>';
            echo '<p><strong>Time Found:</strong> ' . htmlspecialchars($item['time_found']) . '</p>';
            echo '<small>Date Found: ' . htmlspecialchars($item['date_found']) . '</small>';

            if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                echo '<div style="margin-top:10px;"><img src="' . htmlspecialchars($item['image_path']) . '" alt="Found Item Image" style="max-width:100%; border-radius:8px;"></div>';
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
</script>

</body>
</html>
