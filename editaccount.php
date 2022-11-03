<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      Register
DESCRIPTION:    allows user to register with their username, email and password
HTML CODE SECTIONS:
    1. big header for main page
    2. nav bar and quick access bar
    3. register form
    4. footer -->
<?php 
    // CONNECT TO DB AND START SESSION 
    require_once("includes/library.php");
    $pdo = connectDB();
    session_start();
    sessionCheck();

    // VARIABLE DECLARATION
    $errors = array();
    $filled = array();
    $id = $_SESSION["id"];

    // PREPOPULATE FORM WITH USER INFO IN DB
    $query = "select * from myvid_users where id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$id]);
    $user_info = $stmt->fetch();

    // get user input
    $hash = null;
    $user = $_POST["username"] ?? null;
    $fname = $_POST["firstName"] ?? null;
    $lname = $_POST["lastName"] ?? null;
    $pass =  $_POST["password"] ?? null;
    $email = $_POST["email"] ?? null;
    $confirm_email = $_POST["confirm-email"] ?? null;

    // IF SUBMITTED
    if (isset($_POST["submit"])) {

        // VALIDATE AND UPDATE USER NAME
        $otheruser = filter_var($user, FILTER_SANITIZE_STRING);
        if (isset($user) && strcmp($user, $otheruser) == 0){
            $user = strtolower($user);
            // check if new name is unique
            $query = "SELECT * FROM myvid_users WHERE username = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$user]);
    
            if ($stmt->fetch() != false) {
                $errors["user"] = true; 
            }
            else { 
                $filled["user"] = true;
            }
        }
        // VALIDATE AND UPDATE FIRSTNAME
        $fname = filter_var($fname, FILTER_SANITIZE_STRING);
        if (strlen($fname) !== 0) {
            $fname = strtolower($fname);
            if ((!preg_match("#[a-z]+#", $fname) && !preg_match("#[A-Z]+#", $fname)))                         
            { 
                $errors["fname"] = true; 
            }
            else { 
                $filled["fname"] = true;
            }
        }
        // VALIDATE AND UPDATE LAST NAME
        $lname = filter_var($lname, FILTER_SANITIZE_STRING);
        if (strlen($lname) !== 0) {
            $lname = strtolower($lname);

            if (!preg_match("#[a-z]+#", $lname) && !preg_match("#[A-Z]+#", $lname))                    
            { 
                $errors["lname"] = true; 
            }
            else { 
                $filled["lname"] = true;
            }
        }
        // VALIDATE AND UPDATE EMAIL
        if (strlen($email) != 0) {
            $email = strtolower($email);
            $email = filter_var($email, FILTER_SANITIZE_EMAIL);
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) 
            { 
                $query = "SELECT * FROM myvid_users WHERE email = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$email]);
                if ($stmt->fetch() != false) {
                    $errors["email"] = true; 
                }
            }
            // VALIDATE CONFIRM-EMAIL
            $confirm_email = strtolower($confirm_email);

            if (!isset($confirm_email) || strcmp($email, $confirm_email) != 0) {
                $errors["confirm-email"] = true;
            }
            else {
                $filled["email"] = true;
            }
        }
        // VALIDATE AND UPDATE PASSWORD
        $otherpass = filter_var($pass, FILTER_SANITIZE_STRING);
        if (isset($pass) && strcmp($otherpass, $pass) == 0) {
            if (strlen($pass) > 8) {
                $check_length = true;
            }
            else {
                $check_length = false;
            }
            $check_num = preg_match("#[0-9]+#", $pass);
            $check_lowercase = preg_match("#[a-z]+#", $pass);
            $check_uppercase = preg_match("#[A-Z]+#", $pass); 
            $check_speciachar = preg_match( '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $pass); 
            
            if (($check_length && $check_lowercase && $check_uppercase && $check_speciachar) == false) {
                $errors["pass"] = true;
            }
            else {
                $filled["pass"] = true;
            }
        }
        else {
            $errors["pass"] = true;
        }
        // UPDATE NEW INFORMATION 
  
        if (count($errors) === 0) {
            if ($filled["user"]) {
                // update value
                $query = "UPDATE myvid_users SET username = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$user, $id]);
            }
            if ($filled["fname"]) {
                $query = "UPDATE myvid_users SET firstname = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$fname, $id]);
            }
            if ($filled["lname"]) {
                $query = "UPDATE myvid_users SET lastname = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$lname, $id]);
            }
            if ($filled["email"]) {
                $query = "UPDATE myvid_users SET email = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$email, $id]);
            }
            if ($filled["pass"]) {
                $hash = password_hash($pass, PASSWORD_DEFAULT);
                $query = "UPDATE myvid_users SET password = ? WHERE id = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$hash, $id]);
            }
            header("Location: ./index.php");
            exit();

        }       
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include "includes/headsettings.php"; ?>

    <title>Edit Account</title>

</head>

<body id="register-body">
     <!-- SMALL HEADER FOR SUBPAGE -->
     <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>


    <!-- REGISTER SECTION -->
    <div id="register-section">

        <form action="editaccount.php" method="post">
            <h1>Edit Account</h1>
            <p> Only fill in fields that are to be updated </p>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" minlength = "4" maxlength = "20" value ="<?=$user?>" placeholder = "<?=$user_info["username"]?>">
            <p class="error <?= !isset($errors["user"]) ? "hidden" : "" ?>">Username must be unique.</p>

            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" minlength = "2" maxlength = "10" value ="<?=$fname?>" placeholder = "<?=$user_info["firstname"]?>">
            <p class="error <?= !isset($errors["fname"]) ? 'hidden' : "" ?>">First name can only contain alphabets.</p>


            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" minlength = "2" maxlength = "10" value ="<?=$lname?>" placeholder = "<?=$user_info["lastname"]?>">
            <p class="error <?= !isset($errors["lname"]) ? 'hidden' : "" ?>">Last name can only contain alphabets.</p>


            <label for="password">Enter current password or new password:</label>
            <input type="password" id="password" name="password" minlength = "9" maxlength = "20" value = "<?=$pass?>" >
            <p class="error <?= !isset($errors["pass"]) ? 'hidden' : "" ?>">Passwords must have upper and lowercase letters, numbers, and special characters.</p>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value ="<?=$email?>" placeholder = "<?=$user_info["email"]?>">
            <p class="error <?= !isset($errors["email"]) ? "hidden" : "" ?>">Invalid email or email already exists.</p>

            <label for="confirm-email">Confirm Email:</label>
            <input type="email" id="confirm-email" name="confirm-email" value ="<?=$confirm_email?>" placeholder = "<?=$user_info["email"]?>">
            <p class="error <?= !(isset($errors["confirm-email"])) ? 'hidden' : "" ?>">Email does not match.</p>

            <button class="register-btn" name="submit" type="submit">Edit Account</button>
            <p class="error <?= isset($errors) && !isset($_POST["submit"]) ? "hidden" : "" ?>">All changes are saved.</p>

        </form>

    </div>
    <!-- FOOTER -->
    <footer>
        &copy; 2022 Kristy Rath
    </footer>

</body>

</html>