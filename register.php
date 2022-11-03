<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      Register
DESCRIPTION:    allows user to register with their username, email and password


<?php 
    // CONNECT TO DATABASE
    $errors = array();
    require_once("includes/library.php");
    $pdo = connectDB();

    // DECLARE VARIABLES
    $hash = null;
    $user = $_POST["username"] ?? null;

    $fname = strToLower($_POST["firstName"]) ?? null;
    $lname = strToLower($_POST["lastName"]) ?? null;
    $pass =  $_POST["password"] ?? null;
    $email = $_POST["email"] ?? null;
    $confirm_email = $_POST["confirm-email"] ?? null;

    
    if (isset($_POST["submit"])) {
        // VALIDATE USER NAME
            $otheruser = filter_var($user, FILTER_SANITIZE_STRING);
            if (!$user || strcmp($user, $otheruser) != 0 ) {
                $errors["user"] = true; 
            }
            else {
                $query = "SELECT * FROM myvid_users WHERE username = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$user]);
        
                if ($stmt->fetch() != false) {
                    $errors["user"] = true; 
                }
            }

        if (count($errors) === 0) {
            // VALIDATE FIRST NAME
            $fname = filter_var($fname, FILTER_SANITIZE_STRING);
            if (strlen($fname) === 0 || !((preg_match("#[a-z]+#", $fname) && !preg_match("#[A-Z]+#", $fname))))                          
            { 
                $errors["fname"] = true; 
            }
            // VALIDATE LAST NAME
            $lname = filter_var($lname, FILTER_SANITIZE_STRING);
            if (strlen($lname) === 0 || !((preg_match("#[a-z]+#", $lname) && !preg_match("#[A-Z]+#", $lname))))                     
            { 
                $errors["lname"] = true; 
            }
            // VALIDATE PASSWORD
            $otherpass = filter_var($pass, FILTER_SANITIZE_STRING);
            if (strlen($pass) > 8 && strcmp($otherpass, $pass) == 0 ) {
                $check_length = true;
                $check_num = preg_match("#[0-9]+#", $pass);
                $check_lowercase = preg_match("#[a-z]+#", $pass);
                $check_uppercase = preg_match("#[A-Z]+#", $pass); 
                $check_speciachar = preg_match( '/[\'\/~`\!@#\$%\^&\*\(\)_\-\+=\{\}\[\]\|;:"\<\>,\.\?\\\]/', $pass); 
                
                if (($check_length && $check_lowercase && $check_uppercase && $check_speciachar) == false) {
                    $errors["pass"] = true;
                }
            }
            else {
                $check_length = false;
                $errors["pass"] = true;
            }

            // VALIDATE EMAIL
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
            else { 
                $errors["email"] = true; 
            }
            // VALIDATE CONFIRM-EMAIL
            if (strcmp($email, $confirm_email) != 0) {
                $errors["confirm-email"] = true;
            }
            // INSERT USER INFO INTO DATABASE
            if (count($errors) === 0) {
                $user = strtolower("$user");
                $fname = strtolower("$fname");
                $lname = strtolower("$lname");
                $email = strtolower("$email");

                $query = "INSERT INTO myvid_users (username, firstname, lastname, email, password)
                    VALUES (?, ?, ?, ?, ?); ";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$user, $fname, $lname, $email, password_hash($pass, PASSWORD_DEFAULT)]);
                header("Location: ./login.php");
                exit();
            }
        }       
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include "includes/headsettings.php"; ?>
  
    <script defer src = "scripts/register.js"></script>

    <title>Create Account</title>

</head>

<body id="register-body">
     <!-- SMALL HEADER FOR SUBPAGE -->
     <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>


    <!-- REGISTER SECTION -->
    <div id="register-section">

        <form action="register.php" method="post">
            <h1>Create Account</h1>
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" minlength = "4" maxlength = "20" value = "<?=$user?>">
            <p id = "userNameError" class="error <?= !isset($errors["user"]) ? "hidden" : "" ?>">Username must be unique.</p>

            <label for="firstName">First Name:</label>
            <input type="text" id="firstName" name="firstName" minlength = "2" maxlength = "10" value = "<?=$fname?>">
            <p id = "firstNameError" class="error <?= !isset($errors["fname"]) ? 'hidden' : "" ?>">First name can only contain alphabets.</p>


            <label for="lastName">Last Name:</label>
            <input type="text" id="lastName" name="lastName" minlength = "2" maxlength = "10" value = "<?=$lname?>">
            <p id = "lastNameError" class="error <?= !isset($errors["lname"]) ? 'hidden' : "" ?>">Last name can only contain alphabets.</p>


            <label for="password">Password:</label>
            <input type="password" id="password" name="password" minlength = "9" maxlength = "20" value = "<?=$pass?>">
            <div id="strengthField" class="hidden">
                <div class="container">
                    <ul>
                        <li id="length" class="requirements">Between 9 to 20 characters</li>
                        <li id="lowercase" class="requirements">At least 1 lowercase letter</li>
                        <li id="uppercase" class="requirements">At least 1 uppercase letter</li>
                        <li id="number" class="requirements">At least 1 numerical number</li>
                        <li id="special" class="requirements">At least 1 special character</li>
                    </ul>
                </div>
            </div>
            <p id = "passwordError" class="error <?= !isset($errors["pass"]) ? 'hidden' : "" ?>">Password does not meet requirements.</p>

            <label for="email">Email:</label>
            <input type="email" id="email" name="email" value = "<?=$email?>">
            <p id="emailError" class="error <?= !isset($errors["email"]) ? "hidden" : "" ?>">Invalid email or email already exists.</p>

            <label for="confirm-email">Confirm Email:</label>
            <input type="email" id="confirm-email" name="confirm-email" value = "<?=$confirm_email?>">
            <p id="confirmEmailError" class="error <?= !(isset($errors["confirm-email"])) ? 'hidden' : "" ?>">Email does not match.</p>


            <button class="register-btn" name="submit" type="submit">Create Account</button>
        </form>

    </div>
    <!-- FOOTER -->
    <footer>
        &copy; 2022 Kristy Rath
    </footer>

</body>

</html>