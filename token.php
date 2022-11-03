<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      Reset Password
DESCRIPTION:    Allows users to reset their passwords by sending a link to their emails.
HTML CODE SECTIONS:
    1. small header
    2. nav bar and quick access bar
    3. register section
    5. footer -->




<?php 
// CONNECT TO DB AND SESSION 
require_once("includes/library.php");
$pdo = connectDB();
session_start();


// variable declaration 
$new_pass = $_POST["new-password"] ?? null;
$_SESSION["token-input"] = $_POST["token"] ?? null;
$email = $_SESSION["email"] ?? null;
if (isset($_POST["submit-token"])) {

    // check if token expired
    $duration = microtime(true) - $_SESSION["token-creation"];
    // if expired, delete token
    if (microtime(true) -  $_SESSION["token-creation"]  > 600) {
        $_SESSION["token"] = null;
        $_SESSION["token-creation"] = null;  
        $_SESSION["token-expired"] = true;
      }
      else {
        // compare input to token
        $_SESSION["token-expired"] = false;
        if (strcmp($_SESSION["token"], $_SESSION["token-input"]) != 0) {
            $errors["token"] = true;
        }
    }
}
if (isset($_POST["submit-password"]) && !isset($errors["token"])){
    // validate new password
    $other_new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);

    if (strlen($new_pass) > 8 && strcmp($other_new_pass, $new_pass) == 0) {
        $check_length = true;
    }
    else {
        $check_length = false;
    }
    $check_num = preg_match("#[0-9]+#", $new_pass);
    $check_lowercase = preg_match("#[a-z]+#", $new_pass);
    $check_uppercase = preg_match("#[A-Z]+#", $new_pass); 
    $check_speciachar = preg_match( '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $new_pass); 
    
    if (($check_length && $check_lowercase && $check_uppercase && $check_speciachar) == false) {
        $errors["pass"] = true;

    }
    else { 
        // IF VALID, HASH PASSWORD AND STORE IN DATABASE    
        $hash = password_hash($new_pass, PASSWORD_DEFAULT);
        $query = "UPDATE myvid_users SET password = ? WHERE email = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$hash, $email]);
        header("Location: ./logout.php");
        exit();
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include "includes/headsettings.php"; ?>

    <title>Reset Password</title>

</head>

<body id="forgot-body">
     <!-- SMALL HEADER FOR SUBPAGE -->
     <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>


    <!-- REGISTER SECTION -->
    <div id= "forgot-section">
        <form class="<?= (isset($_POST["submit-token"]) && !isset($errors["token"])) ? "hidden" : "" ?>" action = "token.php" method = "post">
            <h1>Enter Token</h1>
            <p>A link will be sent to your email to reset your password.</p>

            <label for = "password">Enter token (expires in 10 minutes):</label>
            <input type = "text" id ="token" name="token" value="<?=$_SESSION["token-input"]?>">
            <p class="error <?= !isset($errors["token"]) ? 'hidden' : "" ?>">Token is invalid.</p>
            <button class="register-btn" name ="submit-token" type = "submit">Submit</button>

        </form> 

        <form class="<?= (!isset($_POST["submit-token"]) || isset($errors["token"])) ? "hidden" : "" ?>" action = "token.php" method = "post">
            <h1>New Password</h1>

            <input type = "password" id ="password" minlength = 9 maxlength = 20 name="new-password">
            <p class="error <?= !isset($errors["pass"]) ? 'hidden' : "" ?>">Passwords must have upper and lowercase letters, numbers, and special characters.</p>
            <button class="register-btn" name ="submit-password" type = "submit">Change Password</button>
        </form> 

    </div>
    <!-- FOOTER -->
    <footer id="forgot-footer">
        &copy; 2022 Kristy Rath
    </footer>
    
</body>
</html>