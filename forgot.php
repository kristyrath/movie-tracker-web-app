<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      Reset Password
DESCRIPTION:    Allows users to reset their passwords by sending a link to their emails.
<?php 
// CONNECT TO DB
require_once("includes/library.php");
require_once ("Mail.php");
$pdo = connectDB();
session_start();
// VARIABLE DECLARATION
$email = $_POST["email"] ?? null;
$token_input = $_POST["token"] ?? null;


if (isset($_POST["submit-email"])) {
    // VALIDATE EMAIL 
    if (isset($email)) {
        $email = strtolower($email);
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            // CHECK IF EMAIL EXISTS IN DB
            $query = "SELECT * FROM myvid_users WHERE email = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$email]);
            $results = $stmt->fetch();

            if (count($results) != 0) {
                // CREATE TOKEN AND RECORD CREATION TIME
                $id = $results["id"];
                $_SESSION["token"] = bin2hex(random_bytes(20)); 
                $_SESSION["token-creation"] = microtime(true);
                $_SESSION["email"] = $email;
                // MAIL TO USER 
                $from = "MyVid Password Reset <noreply@loki.trentu.ca>";
                $to = $email;   
                $subject = "MyVid Password Reset";
                $body =  "Use this token to reset your password:".$_SESSION["token"];
                $host = "smtp.trentu.ca";
                $headers = array ('From' => $from,
                'To' => $to,
                'Subject' => $subject);
                $smtp = Mail::factory('smtp',
                array ('host' => $host));
                
                $mail = $smtp->send($to, $headers, $body);
                if (PEAR::isError($mail)) {
                    // echo("<p>" . $mail->getMessage() . "</p>");
                } else {
                    header("Location: ./token.php");
                    exit();
                }    
            }
            else {
                $errors["email"] = true;
            }
     
        } 
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
        <form action ="forgot.php" method = "post">
            <h1>Reset Password</h1>
            <p>A link will be sent to your email to reset your password.</p>

            <!-- EMAIL AND PASSWORD INPUT -->
            <label for = "email">Email:</label>
            <input type = "email" id ="email" name="email">
            <p class="error <?= !isset($errors["email"]) ? 'hidden' : "" ?>">An account does not exist with the corresponding email.</p>

            <button class="register-btn" name = "submit-email" type = "submit">Request Password Reset</button>
        </form>
     
    </div>
    <!-- FOOTER -->
    <footer id="forgot-footer">
        &copy; 2022 Kristy Rath
    </footer>
    
</body>
</html>