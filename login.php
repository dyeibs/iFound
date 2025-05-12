<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - iFound</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="style.css?v=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<header>
    <div class="logo">
        <img src="SPCF Logo.png" alt="SPCF Logo"> 
        <h1>iFound.</h1>
    </div>
</header>

<div class="main-content">
    <div class="left-side">
        <h1>Lost it?<br><span>iFound it!</span></h1>
        <p>SPCF Exclusive Lost and Found Management System.<br>We've got you.</p>
    </div>

    <div class="login-box">
        <h2>LOGIN</h2>
        <form action="authenticate.php" method="POST" onsubmit="return validateCaptcha();">
        <form action="authenticate.php" method="POST">
            
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>

            <div class="input-box">
                <i class="fa-solid fa-id-card"></i>
                <input type="number" name="student_id" placeholder="Student/Employee ID" required 
                    minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)">
            </div>
            <div class="input-box">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>

            <div class="captchadisplay">
            <div class="g-recaptcha" data-sitekey="6Le-kTUrAAAAANuKi1dCwZrx2-6Nj-5SawJ1i696"></div>
            </div>
            <button type="submit">Login</button>
            <a href="register.php" class="btn-signup">Don't have an account? Sign up</a>
        </form>
    </div>
</div>

<footer>
    Developed by John Ver Elideros & Lee Yan Paul Fukuda | CpE Students, Systems Plus College Foundation
</footer>

<script>
function validateCaptcha() {
    var response = grecaptcha.getResponse();
    if (response.length === 0) {
        alert("Please complete the CAPTCHA.");
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}
</script>

</body>
</html>
