<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      MyVid Collection
DESCRIPTION:    Main page with user favourites and displays user's video collection.
HTML CODE SECTIONS:
    1. big header for main page
    2. nav bar and quick access bar
    4. favourites section
    5. video collection
    6. next and previous buttons
    7. footer -->
    
<?php 
// CONNECT TO DB
require_once("includes/library.php");
$pdo = connectDB();
session_start();
sessionCheck();
$hide_favourites = false;

// CHECK IF SESSION HAS STARTED
if (!isset($_SESSION["id"])) {
    header("Location: ./login.php");
    exit();
}
else {
    $user_id = $_SESSION["id"];
    // SELECT ALL MOVIES IN USER ACCOUNT
    $query = "select * from myvid_movies where user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $results = $stmt->fetchAll();

    // SELECT ID OF FAVOURITE MOVIE
    $query = "select max(movie_id) from myvid_movies where user_id = ? and rating = 5";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id]);
    $favourite_id = $stmt->fetch();
    $f_id = $favourite_id["max(movie_id)"];

    // SELECT INFO OF FAVOURITE MAVIE
    $query = "select * from myvid_movies where user_id = ? and movie_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$user_id, $f_id]);
    $favourite_info = $stmt->fetch();

    // IF USER HAS NO FAVOURITES, HIDE FAVOURITES SECTION
    if (!($favourite_info)) {
        $hide_favourites = true;
    }
    else {
        $hide_favourites = false;
    }


}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <?php include "includes/headsettings.php"?>
    <script defer src = "scripts/index.js"> </script>

    <title>MyVid Collection</title>

</head>

<body id="index-body">
    <!-- BIG HEADER FOR MAIN PAGE -->

    <?php include "includes/header.php" ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php" ?>

    <!-- FAVOURITES SECTION -->
    <!-- NOTE section is incomplete, will complete in the future -->
    <?php if ($hide_favourites != true) : ?>
        <div id="favourites-section" class = "<?= $hide_favourites == true ? "hidden" : "" ?>">
            <h1>RECENT FAVOURITE</h1>
            <div class="carousel">
                <div class="slide">
                    <!-- <a href="#prevSlide"><i class="clickables fa-solid fa-chevron-left fa-2x"></i></a>
                    <a href="#nextSlide"><i class="clickables fa-solid fa-chevron-right fa-2x"></i></a> -->
                    <div>
                        <div class="favourites-img">
                            <a href="details.php?id=<?=$f_id?>">
                                <img class="movieCover"
                                    src="<?=selectCover($favourite_info)?>"
                                    alt="<?=$favourite_info["title"]?> movie cover">
                            </a>
                        </div>
                        <div class="favourites-content">
                            <?php if(isset($favourite_id["max(movie_id)"])) : ?>
                                <h2><?=$favourite_info["title"]?></h2>
                            <?php endif; ?>

                            <p class="starRating-img">
                            <?php 
                                for($i = 0; $i < $favourite_info["rating"]; $i++) { 
                                echo "<i class=\"fa-solid fa-star\"></i>";
                                } ?>
                            </p>
                            <h3>Your Review</h3>
                            <p class="favourites-review"><?=$favourite_info["plot_summary"]?></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    <?php endif ?>
    <!-- MOVIE COLLECTION -->
    <h1 class= "movieCollectionHeader">YOUR COLLECTION</h1>
    <div class="movieCollection">
        <!-- MOVIE COVER + MOVIE TITLE + VIEW, EDIT, DELETE ICONS-->
        <?php foreach ($results as $row): ?>
            <div class="movie">
                <a href="details.php?id=<?=$row["movie_id"]?>">
                <img class="movieCover"
                    src="<?=selectCover($row)?>"
                    alt="<?=$row["title"]?> movie cover">
                </a>
                <div class="movie-title-section">
                    <a href="details.php?id=<?=$row["movie_id"]?>">
                        <h3 class="movieTitle"><?=$row["title"]?></h3>
                    </a>
                </div>
                <div class="functionIcons">
                    <a href="details.php?id=<?=$row["movie_id"]?>"><i class="clickables fa-solid fa-eye"></i></a>
                    <a href="editvid.php?id=<?=$row["movie_id"]?>"><i class="clickables fa-solid fa-pen-to-square"></i></a>
                    <a id="delete-movie-<?=$row["movie_id"]?>" class = "deleteIcon"><i class="clickables fa-solid fa-trash"></i></a>
                </div>
            </div>    
        <?php endforeach; ?> 
    </div>

    
    <!-- NEXT AND PREVIOUS BUTTONS -->
    <div class="next-prev-btn-section">
        <a href="#prev">Previous</a>
        <a href="#next">Next</a>
    </div>

    <div class="delete-movie-section">
    
            <h1 id="delete-movie-heading">Are you sure you want to delete this movie?</h1>

            <div class="delete-movie-buttons">
                <button class="cancel-btn" name="cancel" >Cancel</button>
                <form id="delete-movie-form" action="" method="post">
                <button class="deleteMovie-btn" name="deleteMovie" type="submit">Delete Movie</button>
                </form>
            </div>
    </div>
    <!-- FOOTER -->
    <footer>&copy; Kristy Rath 2022 </footer>
</body>

</html>