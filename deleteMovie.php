<?php
// CONNECT TO DB
require_once("includes/library.php");
$pdo = connectDB();
session_start();
sessionCheck();
$uri = $_SERVER['REQUEST_URI'];
// VALIDATE AND SANITIZE URL 
$uri = filter_var($uri, FILTER_SANITIZE_URL);
if (filter_var($uri, FILTER_VALIDATE_URL)) {
    $errors["uri"] = true;
    header("Location: ./index.php");
    exit();
}
// GET ID FROM URI
$var = parse_url($uri);
$str = explode("=",$var["query"]);
$movie_id = $str[1];
$id = $_SESSION["id"];

// CHECK IF USER OWNS VIDEO
$query = "select * from myvid_movies where movie_id = ? and user_id = ?";
$stmt = $pdo->prepare($query);
$results = $stmt->execute([$movie_id, $id]);

// IF USER OWNS VIDEO, DELETE
if (isset($results)) {
    $query = "delete from myvid_genre where movie_id = ?";
    $pdo->prepare($query)->execute([$movie_id]);

    $query = "delete from myvid_type where movie_id = ?";
    $pdo->prepare($query)->execute([$movie_id]);

    $query = "delete from myvid_movies where movie_id = ?";
    $pdo->prepare($query)->execute([$movie_id]);

    header("Location: ./index.php");
    exit();
}


?>