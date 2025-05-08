<?php
session_start();
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Report Found Item</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="SPCF Logo.png" alt="SPCF Logo">
        <h1>iFound.</h1>
    </div>
</header>

<div class="main-content">
    <div class="login-box">
        <h2>Report Found Item</h2>
        <form action="submit_found_item.php" method="POST" enctype="multipart/form-data">
            <div class="multiselect-box">
                <label><strong>Select Item Type:</strong></label>
                <div class="checkbox-group">
                    <label><input type="radio" name="item_type" value="Laptop"> Laptop</label>
                    <label><input type="radio" name="item_type" value="Smartphone"> Smartphone</label>
                    <label><input type="radio" name="item_type" value="Wallet"> Wallet</label>
                    <label><input type="radio" name="item_type" value="Bag"> Bag</label>
                    <label><input type="radio" name="item_type" value="Clothes"> Clothes</label>
                    <label><input type="radio" name="item_type" value="Accessories"> Accessories</label>
                    <label><input type="radio" name="item_type" value="Others" id="othersRadio"> Others</label>
                </div>
                <input type="text" name="other_item_type" id="other-item-type" placeholder="Please specify">
            </div>

            <div class="input-box">
                <label><strong>Add a Picture:</strong></label>
                <input type="file" name="item_image" accept="image/*" required>
            </div>

            <div class="input-box">
                <textarea name="description" rows="3" placeholder="Description" required></textarea>
            </div>

            <div class="input-box">
                <input type="time" name="time_found" required>
            </div>

            <div class="input-box">
                <input type="text" name="location_found" placeholder="Where did you find it?" required>
            </div>

            <div class="input-box">
                <input type="date" name="date_found" required>
            </div>
            
            <button type="submit">Submit</button>
            <a href="dashboard.php" class="btn-signup">Back to Dashboard</a>
        </form>
    </div>
</div>

<script>
    const othersRadio = document.getElementById('othersRadio');
    const otherItemType = document.getElementById('other-item-type');

    document.querySelectorAll('input[name="item_type"]').forEach(radio => {
        radio.addEventListener('change', function () {
            if (othersRadio.checked) {
                otherItemType.style.display = 'block';
            } else {
                otherItemType.style.display = 'none';
                otherItemType.value = '';
            }
        });
    });
</script>

</body>
</html>
