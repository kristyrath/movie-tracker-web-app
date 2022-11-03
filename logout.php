
<?php 
    // Log out 

    // starts session to access session variables
    session_start();
    // unset all variables
    session_unset();      
    // end session
    session_destroy();
    header("Location: ./login.php");
    exit();
?>