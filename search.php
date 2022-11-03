<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      Register
DESCRIPTION:    includes a search form for user input and a result display section 
HTML CODE SECTIONS:
    1. big header for main page
    2. nav bar and quick access bar
    3. search form 
    3. result section
    4. footer -->

<?php 
// START DB
require_once("includes/library.php");
$pdo = connectDB();
session_start();
sessionCheck();

// VARIABLE DECLARTION 
$id = $_SESSION["id"];
$filt_by_rating = $_POST["filt_by_rating"] ?? null;
$search = $_POST["search"] ?? null;
$results = null;
$results_heading = "Results";


if (isset($_POST["submit"])) {

    // CHECK IF FILTER BY RATING IS SELECTED
    isset($filt_by_rating) ? $filt_by_rating = 1 : $filt_by_rating = 0;

    if (isset($search)) {

        // SANITIZE SEARCH INPUT
        $search = filter_var($search, FILTER_SANITIZE_STRING);
        if ($search == "") {
            $errors["search"] = true;
        }
        else { 
            // SPLIT SEARCH INTO CHUNKS
            $splt_title = str_split($search, 4);

            // FORM QUERY
            $query = "SELECT * from myvid_movies where user_id = ? and (title like ? ";
            
            // DECLARE ARRAY OF PARAMETERS
            $execute_vars = array();
            $execute_vars[0] = $id;
            $search_wc = "%".$search."%";
            $execute_vars[1] = $search_wc;
            // concatenate each chunk to the query
            foreach ($splt_title as $chunk) {
                $query = $query." or title like ? ";
            }
            $j = 2;

            // assign values to array of parameters
            for ($i = 0; $i < count($splt_title); $i++) {
                
                $execute_vars[$j] = "%".$splt_title[$i]."%";
                $j++;
            } 
            $query = $query.")";

            // if filter by rating is selected, add another query specification
            if ($filt_by_rating == 1) {
                $query = $query." order by rating desc";
                
            }
            // select search results
            $stmt = $pdo->prepare($query);
            $stmt->execute($execute_vars);
            $results = $stmt->fetchAll();

            if (!isset($results)) {
                $results_heading = "No results found";
            }

        }

    }

}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/headsettings.php"; ?>

    <title>Search</title>

</head>

<body id="search-body">
     <!-- SMALL HEADER FOR SUBPAGE -->
     <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>

    <!-- SEARCH FORM SECTION -->
    <div id= "search-section">
        <form action = "search.php" method = "post">
            <!-- TITLE INPUT-->
            <div>
                <i class="fa-solid fa-magnifying-glass fa-3x"></i>
                <input class="search-input" type="text" name="search" placeholder="Enter Search Here" value="<?=$search?>">
            </div>

            <!-- SEARCH BUTTON -->
            <button class="search-section-search-btn" name="submit" type = "submit">Search</button>
            <div class=filter>
            <input type="checkbox" name="filt_by_rating" value="1" <?=$filt_by_rating == 1 ? "checked" : ""?>><p>Filter By Rating</p>
        </div>
        </form>
        <!-- SEARCH RESULT SECTION (GRID) -->

 
        <div class="search-result-section">
            <h1><?=$results_heading?> </h1>
            <?php if (isset($results)) : ?>
                <?php foreach ($results as $row): ?>
                <div class="movie">
                    <a href="details.php?id=<?=$row["movie_id"]?>">
                    <img class="movieCover"
                        src="<?=selectCover($row)?>"
                        alt="<?=$row["title"]?> movie cover">
                    </a>
                    <div class="movie-title-section">
                        <a href="#movieTitle">
                            <h3 class="movieTitle"><?=$row["title"]?></h3>
                        </a>
                    </div>
                    <div class="functionIcons">
                        <a href="details.php?id=<?=$row["movie_id"]?>"><i class="clickables fa-solid fa-eye"></i></a>
                        <a href="editvid.php?id=<?=$row["movie_id"]?>"><i class="clickables fa-solid fa-pen-to-square"></i></a>
                        <a href="deleteMovie.php?id=<?=$row["movie_id"]?>"><i class="clickables fa-solid fa-trash"></i></a>
                    </div>
                </div>    
                <?php endforeach; ?> 
            <?php endif; ?>


        </div>
    </div>
    
    <!-- FOOTER -->
    <footer>
        &copy; 2022 Kristy Rath
    </footer>
    
</body>
</html>