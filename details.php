<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      details
DESCRIPTION:        Allow viewers to view records on their videos that they have submitted. 
HTML CODE SECTIONS: 
    1. small header
    2. nav and quick access bar
    3. favourites carousel
    4. movie details section
        a. movie cover 
        b. movie text details
        c. plot summary section
    5. footer
 -->

<?php 
// CONNECT TO DB AND SESSION 
require_once("includes/library.php");
$pdo = connectDB();
session_start();
sessionCheck();

// GET USER ID
$user_id = $_SESSION["id"];

// CHECK IF SESSION STARTED
if (!isset($_SESSION["id"])) {
    header("Location: ./index.php");
    exit();
}

// GET MOVIE ID FROM URL
$uri = $_SERVER['REQUEST_URI'];
$uri = filter_var($uri, FILTER_SANITIZE_URL);
if (filter_var($uri, FILTER_VALIDATE_URL)) {
    $errors["uri"] = true;
    throw new Invalid_Url_Exception("Url is invalid");
    header("Location: ./index.php");
    exit();
}
$var = parse_url($uri);
$str = explode("=",$var["query"]);
$movie_id = $str[1];

// SELECT MOVIE
$query = "select * from myvid_movies where movie_id = ? and user_id = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$movie_id, $user_id]);
$movie_info = $stmt->fetch();

// IF MOVIE DOES NOT EXIST, RECORD ERROR
if ($movie_info == false) {

    $errors["movie"] = true;
    header("Location: ./index.php");
    exit();
}
else {
    // ASSIGN MPAA_RATING TO STRING
    switch($movie_info["mpaa_rating"]) {
        case "g":
            $mpaa_rating = "General";
            break;
        case "m":
            $mpaa_rating = "Mature";
            break;
        case "pg":
            $mpaa_rating = "Parental Guidance";
            break;
        case "r":
            $mpaa_rating = "Restricted";
            break;
        case "x":
            $mpaa_rating ="NC-17";
            break;

    }
    // ASSIGN GENRE TO STRING
    $genre_string = "";
    if ($genre_info["drama"]=1) { $genre_string."Drama "; }
    if ($genre_info["action"]=1) { $genre_string."Action "; }
    if ($genre_info["horror"]=1) { $genre_string."Horror "; }
    if ($genre_info["comedy"]=1) { $genre_string."Comedy "; }
    if ($genre_info["thriller"]=1) { $genre_string."Thriller, "; }
    if ($genre_info["scifi"]=1) { $genre_string."Sci-fi "; }
    if ($genre_info["romance"]=1) { $genre_string."Romance "; }

    // SELECT GENRE INFO FROM DB  
    $query = "select * from myvid_genre where movie_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$movie_id]);
    $genre_info = $stmt->fetch();

    // SELECT TYPE INFO FROM DB 
    $query = "select * from myvid_type where movie_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$movie_id]);
    $type_info = $stmt->fetch();

    // GET MOVIE COVER
    $img_name = explode("/", $movie_info["img_data"]);

    if ($movie_info["isURL"] != 1 ) {
        $selected_cover = "https://loki.trentu.ca/~kristyrath/www_data/".$img_name[count($img_name)-1];
    }
    else {
        $selected_cover = $movie_info["img_url"];
    }
}    



?>


<!DOCTYPE html>
<html lang="en">

<head>
<?php include "includes/headsettings.php"; ?>

    <title>Details</title>

</head>

<body>
    <!-- SMALL STYLE HEADER FOR SUBPAGE -->
     <!-- SMALL HEADER FOR SUBPAGE -->
     <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>


    <!-- MOVIE DETAILS SECTION -->
    <div id="movieDetails-section" >
        <!-- BACKGROUND CONTAINER FOR CSS STYLING -->
        <div class="blurred-bg">
            <!-- Style is included here because the filter has to be applied to the image while being dynamic -->
            <style>
                .blurred-bg {
                    background-image: url("<?=$selected_cover?>");
                }
            </style>
        </div>
            <div class="movieDetails">
                <!-- MOVIE COVER -->
                <div>
                    <a href="editvid.php?id=<?=$movie_id?>">

                        <img class="movieCover-details"
                            src="<?=$selected_cover?>"
                            alt="<?=$movie_info["title"]?> movie cover" width=150 height=200>
                    </a>
                </div>
                <!-- MOVIE TEXT DETAILS -->
                <div class="main-details">
                    <!-- TITLE -->
                    <h1><?=$movie_info["title"]?></h1>
                    <ul>
                        <!-- STAR RATING -->
                        <li>
                            <?php 
                            for($i = 0; $i < $movie_info["rating"]; $i++) { 
                            echo "<i class=\"fa-solid fa-star\"></i>";
                            } ?>
         
                        </li>
                        <!-- VIDEO TYPE -->
                        <li>
                           
                            <i class="fa-solid fa-circle-<?=$type_info["dvd"] == 1 ? "check" : "xmark" ?>"></i> DVD
                            <i class="fa-solid fa-circle-<?=$type_info["bluray"] == 1 ? "check" : "xmark" ?>"></i> BluRay
                            <i class="fa-solid fa-circle-<?=$type_info["4k"] == 1 ? "check" : "xmark" ?>"></i> 4k
                            <i class="fa-solid fa-circle-<?=$type_info["digital_sd"] == 1 ? "check" : "xmark" ?>"></i> Digital SD
                            <i class="fa-solid fa-circle-<?=$type_info["digital_hd"] == 1 ? "check" : "xmark" ?>"></i> Digital HD
                            <i class="fa-solid fa-circle-<?=$type_info["digital_4k"] == 1 ? "check" : "xmark" ?>"></i> Digital 4k

                        </li>
                        <!-- OTHER TEXT DETAILS -->
                        

                        <li>MPAA: <span><?=$mpaa_rating?></span></li>
                        <li>Year:<span><?=$movie_info["year"]?></span></li>
                        <li>Run Time (mn):<span><?=$movie_info["runtime"]?></span></li>
                        <li>Theatrical Release: <span><?=$movie_info["theatrical_release"]?></span></li>
                        <li>DVD/Streaming Release: <span><?=$movie_info["streaming_release"]?></span></li>
                        <li>Studio: <span><?=$movie_info["studio"]?></span></li>
                        <li>Actors: <span><?=$movie_info["actors"]?></span></li>
                        <li>Genres: <span>Action</span></li>
                    </ul>
                </div>
                <!-- PLOT SUMMARY SECTION -->
                <div class="details-summary">
                    <h3>Summary</h3>
                    <p class="summary"><?=$movie_info["plot_summary"]?>
                    </p>
                </div>
            </div>
       
    </div>

    <!-- FOOTER -->
    <footer>
        &copy; 2022 Kristy Rath
    </footer>
</body>

</html>