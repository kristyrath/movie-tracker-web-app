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
    // CONNECT TO DB
    $errors = array();
    require("includes/library.php");
    $pdo = connectDB();
    session_start();
    sessionCheck();

    // GET USER ID
    $id = $_SESSION["id"];
    
    // DELETE ALL RECORDS OWNED BY USER
    if (isset($_POST["submit"])) {

        $query = "delete from myvid_type where user_id = $id";
        $pdo->query($query);

        $query = "delete from myvid_genre where user_id = $id";
        $pdo->query($query);

        $query = "delete from myvid_movies where user_id = $id";
        $pdo->query($query);
        
        $query = "delete from myvid_users where id = $id";
        $pdo->query($query);

  
        // LOGOUT AND END SESSION 
        session_destroy();
        header("Location: ./login.php");
        exit();
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
<?php include "includes/headsettings.php"; ?>
    <title>Delete Account</title>
</head>
<body id="delete-body">
     <!-- SMALL HEADER FOR SUBPAGE -->
     <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>


    <!-- DELETE ACCOUNT SECTION -->
    <div id="delete-section">
        <form action="deleteaccount.php" method="post">
            <h1 id="delete-heading">Are you sure you want to delete your account?</h1>
            <button class="register-btn" name="submit" type="submit">Delete Account</button>
        </form>
    </div>
    <!-- FOOTER -->
    <footer>
        &copy; 2022 Kristy Rath
    </footer>
</body>
</html>