<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="style.css">
<link rel="stylesheet" href="style.css?v=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            <p>SPCF Exclusive Lost and Found Management System.<br> We've got you.</p>
        </div>
<body>
    <div class="container">
        <div class="login-box">
            <h2>REGISTER</h2>
            <form action="signup.php" method="POST">

            <div class="input-box">
                <i class="fa-solid fa-id-card"></i>
                <input type="number" name="student_id" placeholder="Student/Faculty ID" required 
                minlength="10" maxlength="10" oninput="this.value=this.value.slice(0,10)">
            </div>
            <div class="input-box">
                <i class="fa-solid fa-mobile-screen-button"></i>
                <input type="number" name="mobilenumber" placeholder="Mobile Number" required 
                minlength="11" maxlength="11" oninput="this.value=this.value.slice(0,11)">
            </div>   
            <div class="input-box">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="firstname" placeholder="First Name" required>
            </div>
            <div class="input-box">
                <i class="fa-solid fa-user"></i>
                <input type="text" name="lastname" placeholder="Last Name" required>
            </div>   
            <div class="input-box">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <div class="input-box">
                <i class="fa-solid fa-lock"></i>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
            </div>
                <button type="submit">Sign Up</button>
                <a href="login.php" class="btn-signup">Already have an account? Login</a>
            </form>
            
        </div>
    </div>
    
</body>
</html>
