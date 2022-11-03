<?php

// PHP USED TO CHECK UNIQUENESS OF EMAIL

$email = $_GET['email'] ?? null;

// sanitizes email
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo 'error';
  return;
}

// creates db connection 
require_once './includes/library.php';
$pdo = connectDB();

// selects email
$statement = $pdo->prepare("SELECT * FROM myvid_users WHERE email = ?");
$statement->execute([$email]);


// if email already exists return true which is an error
if ($statement->fetch()) echo 'true';
else echo 'false';
 