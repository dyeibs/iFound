<?php
session_start();

if (!isset($_SESSION['student_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
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



// Handle approval/denial actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['item_id'], $_POST['action'])) {
    $item_id = intval($_POST['item_id']);
    $action = $_POST['action'] === 'approve' ? 1 : -1;

    $table = isset($_POST['item_type']) && $_POST['item_type'] === 'found' ? 'found_items' : 'lost_items';
    $stmt = $conn->prepare("UPDATE $table SET is_approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $action, $item_id);
    $stmt->execute();
    $stmt->close();
}

// Get all pending lost and found items
$lost_query = "
    SELECT lost_items.*, users.mobilenumber 
    FROM lost_items 
    JOIN users ON lost_items.student_id = users.student_id 
    WHERE lost_items.is_approved = 0 
    ORDER BY lost_items.date_lost DESC
";
$lost_result = $conn->query($lost_query);

$found_query = "
    SELECT found_items.*, users.mobilenumber 
    FROM found_items 
    JOIN users ON found_items.student_id = users.student_id 
    WHERE found_items.is_approved = 0 
    ORDER BY found_items.date_found DESC
";
$found_result = $conn->query($found_query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - iFound</title>
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
            <li><a href="archive_old_found_items.php"><i class="fa-solid fa-box-archive"></i> View Archived Item/s</a></li>
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
    <h2>Welcome to Admin Dashboard!</h2>
    <p>You can review lost & found reports, Pending reports, and Feedbacks.</p>


    <div style="text-align: center; margin: 20px 0;">
    <a href="javascript:void(0);" onclick="toggleSection('lost-section')" class="btn-lost">View Pending Lost Items</a>
    <a href="javascript:void(0);" onclick="toggleSection('found-section')" class="btn-primary">View Pending Found Items</a>
    <a href="archive_old_found_items.php" class="btn-approve" style="margin-left: 10px;">View Archived</a>
</div>


    <!-- Pending Lost Items -->
    <div id="lost-section">
        <h3 style="text-align: center; margin-top: 30px;">Pending Lost Items</h3>
        <?php if ($lost_result && $lost_result->num_rows > 0): ?>
            <div class="items-container">
                <?php while ($item = $lost_result->fetch_assoc()): ?>
                    <div class="item-card">
                        <h4><?= htmlspecialchars($item['item_type']) ?></h4>
                        <p><strong>Student ID:</strong> <?= htmlspecialchars($item['student_id']) ?></p>
                        <p><strong>Mobile Number:</strong> <?= htmlspecialchars($item['mobilenumber']) ?></p>
                        <p><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
                        <p><strong>Location Lost:</strong> <?= htmlspecialchars($item['location_lost']) ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($item['date_lost']) ?> | <strong>Time:</strong> <?= htmlspecialchars($item['time_lost']) ?></p>

                        <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                            <div style="margin-top:10px;"><img src="<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image" style="max-width:100%; border-radius:8px;"></div>
                        <?php endif; ?>

                        <form method="post" style="margin-top: 10px; text-align: center;">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="item_type" value="lost">
                            <button type="submit" name="action" value="approve" class="btn-approve">Approve</button>
                            <button type="submit" name="action" value="reject" class="btn-deny">Reject</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center;">No pending lost items for approval.</p>
        <?php endif; ?>
    </div>

    <!-- Pending Found Items -->
    <div id="found-section">
        <h3 style="text-align: center; margin-top: 30px;">Pending Found Items</h3>
        <?php if ($found_result && $found_result->num_rows > 0): ?>
            <div class="items-container">
                <?php while ($item = $found_result->fetch_assoc()): ?>
                    <div class="item-card">
                        <h4><?= htmlspecialchars($item['item_type']) ?></h4>
                        <p><strong>Student ID:</strong> <?= htmlspecialchars($item['student_id']) ?></p>
                        <p><strong>Mobile Number:</strong> <?= htmlspecialchars($item['mobilenumber']) ?></p>
                        <p><strong>Description:</strong> <?= htmlspecialchars($item['description']) ?></p>
                        <p><strong>Location Found:</strong> <?= htmlspecialchars($item['location_found']) ?></p>
                        <p><strong>Date:</strong> <?= htmlspecialchars($item['date_found']) ?> | <strong>Time:</strong> <?= htmlspecialchars($item['time_found']) ?></p>

                        <?php if (!empty($item['image_path']) && file_exists($item['image_path'])): ?>
                            <div style="margin-top:10px;">
                                <img src="<?= htmlspecialchars($item['image_path']) ?>" alt="Item Image" style="max-width:100%; border-radius:8px;">
                            </div>
                        <?php endif; ?>

                        <form method="post" style="margin-top: 10px; text-align: center;">
                            <input type="hidden" name="item_id" value="<?= $item['id'] ?>">
                            <input type="hidden" name="item_type" value="found">
                            <button type="submit" name="action" value="approve" class="btn-approve">Approve</button>
                            <button type="submit" name="action" value="reject" class="btn-deny">Reject</button>
                        </form>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p style="text-align: center;">No pending found items for approval.</p>
        <?php endif; ?>
    </div>

    <!-- Lost Items -->
    <h3 style="text-align: center; margin-top: 30px;">Recent Lost Items</h3>
    <div class="items-container">
    <?php
    $sql_items = "
        SELECT lost_items.*, users.mobilenumber 
        FROM lost_items 
        JOIN users ON lost_items.student_id = users.student_id 
        WHERE lost_items.is_approved = 1 
        ORDER BY lost_items.date_lost DESC 
        LIMIT 5";


    $result_items = $conn->query($sql_items);

    if ($result_items && $result_items->num_rows > 0) {
        while ($item = $result_items->fetch_assoc()) {
            echo '<div class="item-card">';
            echo '<h4>' . htmlspecialchars($item['item_type']) . '</h4>';
            echo '<p><strong>Student ID:</strong> ' . htmlspecialchars($item['student_id']) . '</p>';
            echo '<p><strong>Mobile Number:</strong>' . htmlspecialchars($item['mobilenumber']) .'</p>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($item['other_item_type']) . ' ' .  htmlspecialchars($item['description']) . '</p>';
            echo '<p><strong>Last Located:</strong> ' . htmlspecialchars($item['location_lost']) . '</p>';
            echo '<p><strong>Time Lost:</strong> ' . htmlspecialchars($item['time_lost']) . '</p>';
            echo '<small>Date Lost: ' . htmlspecialchars($item['date_lost']) . '</small>';

            if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                echo '<div style="margin-top:10px;"><img src="' . htmlspecialchars($item['image_path']) . '" alt="Lost Item Image" style="max-width:100%; border-radius:8px;"></div>';
            }
            //remove button naka connect sa remove_lost_item.php
            if ($item['is_approved'] == 1 && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                echo '<form method="POST" action="remove_lost_item.php" onsubmit="return confirm(\'Are you sure you want to remove this item?\');" style="margin-top: 10px;">';
                echo '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
                echo '<button type="submit" class="btn-deny">Remove</button>';
                echo '</form>';
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
    $sql_found = "
        SELECT found_items.*, users.mobilenumber 
        FROM found_items 
        JOIN users ON found_items.student_id = users.student_id 
        WHERE found_items.is_approved = 1 AND found_items.archived = 0 
        ORDER BY found_items.date_found DESC 
        LIMIT 5";

    $result_found = $conn->query($sql_found);

    if ($result_found && $result_found->num_rows > 0) {
        while ($item = $result_found->fetch_assoc()) {
            echo '<div class="item-card">';
            echo '<h4>' . htmlspecialchars($item['item_type']) . '</h4>';
            echo '<p><strong>Student ID:</strong> ' . htmlspecialchars($item['student_id']) . '</p>';
            echo '<p><strong>Mobile Number:</strong> ' . htmlspecialchars($item['mobilenumber']) . '</p>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($item['description']) . '</p>';
            echo '<p><strong>Found At:</strong> ' . htmlspecialchars($item['location_found']) . '</p>';
            echo '<p><strong>Time Found:</strong> ' . htmlspecialchars($item['time_found']) . '</p>';
            echo '<small>Date Found: ' . htmlspecialchars($item['date_found']) . '</small>';
    
            if (!empty($item['image_path']) && file_exists($item['image_path'])) {
                if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                    echo '<div style="margin-top:10px;"><img src="' . htmlspecialchars($item['image_path']) . '" alt="Found Item Image" style="max-width:100%; border-radius:8px;"></div>';
                } else {
                    echo '<p><i>Image available to admin only.</i></p>';
                }
            }
            //remove button naka connect sa remove_found_item.php
            if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                $qr_data = urlencode("Item Type: {$item['item_type']}\nDescription: {$item['description']}\nLocation Found: {$item['location_found']}");
                $qr_url = "https://api.qrserver.com/v1/create-qr-code/?data={$qr_data}&size=150x150";
                echo '<div style="margin-top:10px;">';
                echo '<img src="' . $qr_url . '" alt="QR Code for Item">';
                echo '<p><small>QR Code for this found item (admin only)</small></p>';
                echo '</div>';
            }
            if ($item['is_approved'] == 1 && isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
                echo '<div style="margin-top: 10px; display: flex; gap: 10px;">';

                // Remove button
                echo '<form method="POST" action="remove_found_item.php" onsubmit="return confirm(\'Are you sure you want to remove this item?\');">';
                echo '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
                echo '<button type="submit" class="btn-deny">Remove</button>';
                echo '</form>';

                // Archive button
                echo '<form method="POST" action="archive_old_found_items.php" onsubmit="return confirm(\'Are you sure you want to archive this item?\');">';
                echo '<input type="hidden" name="item_id" value="' . $item['id'] . '">';
                echo '<button type="submit" class="btn-approve">Archive</button>';
                echo '</form>';
                echo '</div>';
            } 
            echo '</div>';
        }
    }
     else {
        echo '<p style="text-align:center;">No found items reported yet.</p>';
    }
    ?>
    </div>

    <!-- Feedback Viewer for Admins -->
    <h3 style="text-align: center; margin-top: 40px;">User Feedback</h3>
    <div class="items-container">
    <?php
    $feedback_query = $conn->query("SELECT * FROM feedback ORDER BY submitted_at DESC");

    if ($feedback_query && $feedback_query->num_rows > 0) {
        while ($fb = $feedback_query->fetch_assoc()) {
            echo '<div class="item-card">';
            echo '<p><strong>Student ID:</strong> ' . htmlspecialchars($fb['student_id']) . '</p>';
            echo '<p><strong>Rating:</strong> ' . str_repeat("⭐", $fb['rating']) . '</p>';
            echo '<p><strong>Comment:</strong> ' . htmlspecialchars($fb['comment']) . '</p>';
            echo '<small>Submitted on: ' . $fb['submitted_at'] . '</small>';
            echo '</div>';
        }
    } else {
        echo '<p style="text-align:center;">No feedback submitted yet.</p>';
    }
?>
</div>
</main>

    <script>
    function toggleSidebar() {
        document.getElementById("sidebar").classList.toggle("active");
    }

    function toggleSection(sectionId) {
        const section = document.getElementById(sectionId);
        section.style.display = (section.style.display === "none") ? "block" : "none";
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
