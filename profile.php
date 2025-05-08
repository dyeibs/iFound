<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
include 'includes/db.php';

$student_id = $_SESSION['student_id'];
$sql = "SELECT firstname, lastname, mobilenumber FROM users WHERE student_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $row = $result->fetch_assoc()) {
    $firstname = $row['firstname'];
    $lastname = $row['lastname'];
    $mobilenumber = $row['mobilenumber'];
} else {
    $firstname = $lastname = $mobilenumber = "N/A";
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile - iFound</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  
</head>
<body>

<header>
    <div class="logo">
        <img src="SPCF Logo.png" alt="SPCF Logo">
        <h1>iFound.</h1>
    </div>
    </div>
</header>

<div class="profile-container">
    <div class="profile-card">
        <h2>User Profile</h2>
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($firstname); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($lastname); ?></p>
        <p><strong>Mobile Number:</strong> <?php echo htmlspecialchars($mobilenumber); ?></p>
        <p><strong>Student ID:</strong> <?php echo htmlspecialchars($student_id); ?></p>

        <a href="dashboard.php"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </div>
</div>

</body>
</html>
