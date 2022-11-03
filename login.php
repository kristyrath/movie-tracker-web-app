<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      Log In
DESCRIPTION:    log in page with a "forgot password" link for users.
HTML CODE SECTIONS:
    1. big header for main page
    2. nav bar and quick access bar
    3. log in form
    4. reset password section
    5. footer -->

<?php
// CONNECT TO DB
require_once("includes/library.php");
$pdo = connectDB();
$errors = array();


// VARIABLE DECLARATION
$user = $_POST["username"] ?? null;
$pass = $_POST["password"] ?? null;
$remember = $_POST["remember"] ?? null;



// IF FORM IS SUBMITTED
if (isset($_POST['submit'])) {

    // VALIDATE USER NAME
    $user = filter_var($user, FILTER_SANITIZE_STRING);
    if (strlen($user) === 0) {
        $errors["username"] = true;
    }

    if (count($errors) === 0) {
        // FETCH ACCOUNT INFORMATION
        $query = "SELECT * from myvid_users where username = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$user]);
        $results = $stmt->fetch();
        // IF NOT FOUND, SET ERROR
        if (!isset($results["username"])) {
            $errors["username"] = true;
        }
        // VALIDATE PASSWORD
        else {
       
            if (password_verify($pass, $results["password"])) {
                // START SESSION 
                session_start(); 
                $id = $results["id"];
                $_SESSION["username"] = $user;
                $_SESSION["id"] = $id;
                // SET COOKIE
                if (isset($remember)) {
                    setcookie("username", $user);
                }
                header("Location: ./index.php");
                exit();
            }
            else {
                $errors['password'] = true;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/headsettings.php"; ?>
    <title>Log In</title>
</head>

<body id="login-body">
    <!-- SMALL HEADER FOR SUBPAGE -->
    <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>

   
    <!-- LOG IN FORM SECTION -->
    <div id="login-section">
        <h1>Log In</h1>
        <form action="login.php" method="post">
            <label for="username"><i class="fa-solid fa-user-large"></i> Username:</label>
            <input type="text" id="username" name="username" minlength = "4" maxlength = "20" value = "<?= isset($_COOKIE["username"]) ? $_COOKIE["username"]: $user?>">

            <label for="password"><i class="fa-solid fa-lock"></i> Password:</label>
            <input type="password" id="password" name="password" minlength = "9" maxlength = "20" value = "<?= $pass?>">

            <div>
                <label for="remember"><input type="checkbox" id="remember" name="remember" value = "<?= $remember ?>">
                    Remember me</label>
            </div>

            <!-- FORGOT PASSWORD REDIRECT -->
            <a href="forgot.php">Forgot password?</a>
            <button class="login-btn" name="submit" type="submit">Sign In</button>
            <p class="error <?= ($errors == false) ? "hidden" : ""?> ">Username or password is invalid.</p>

        </form>
    </div>
    <!-- FOOTER -->
    <footer>
        &copy; 2022 Kristy Rath
    </footer>

</body>

</html>