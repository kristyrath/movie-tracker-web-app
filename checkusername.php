<?php


// PHP CHECKS IF USERNAME EXISTS TO USE FOR USERNAME UNIQUENESS VALIDATION
$username = $_GET['username'] ?? null;

// create database connection
require_once './includes/library.php';
$pdo = connectDB();

// sanitze user inputted string
$username = filter_var($username, FILTER_SANITIZE_STRING);

// query select
$statement = $pdo->prepare("SELECT * FROM myvid_users WHERE username = ?");
$statement->execute([$username]);

if ($statement->fetch()) echo "true";
else echo "false";
 