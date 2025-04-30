
<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<body>
    <header>
        <div class="logo">
            <img src="SPCF Logo.png" alt="SPCF Logo"> <!-- Replace with your logo path -->
            <h1>iFound.</h1>
        </div>
        <div class="menu-icon">☰</div>
    </header>

    <div class="main-content">
        <div class="left-side">
            <h1>Lost it?<br><span>iFound it!</span></h1>
            <p>SPCF Exclusive Lost and Found Management System.<br> We've got you.</p>
        </div>

        <div class="login-box">
            <h2>LOGIN</h2>
            <form action="authenticate.php" method="POST">

        <div class="input-box">
            <i class="fa-solid fa-user"></i>
            <input type="text" name="student_id" placeholder="ID" required>
        </div>
        <div class="input-box">
            <i class="fa-solid fa-lock"></i>
            <input type="password" name="password" placeholder="Password" required>
        </div>

    <a href="#">Forgot Password?</a>
    <button type="submit">Login</button>
    <a href="register.php" class="btn-signup">Dont have an account? Sign in</a>
</form>

        </div>
    </div>

    <footer>
        Developed by John Ver Elideros & Lee Yan Paul Fukuda | CpE Students, Systems Plus College Foundation
    </footer>
</body>
</html>
