<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      Add Video
DESCRIPTION:    Allows user to input new videos and corresponding information into their collection.

<?php 

    // CONNECT TO DB
    require_once("includes/library.php");
    $pdo = connectDB();
    session_start();
    sessionCheck();

    // VARIABLE DECLARATION
    $errors = array();

    // text/number fields
    
    $cover_url = $_POST["cover-url"] ?? null;
    $newname = null;
    $title = $_POST["title"] ?? null;
    $rating = $_POST["rating"] ?? 3;
    $year = $_POST["year"] ?? null;
    $mpaa_rating = $_POST["mpaa-rating"] ?? null;
    $runtime = $_POST["runTime"] ?? null;
    $studio = $_POST["studio"] ?? null;
    $theatrical_release = $_POST["theatrical-release"] ?? null;
    $dvd_release = $_POST["dvd-release"] ?? null;
    $plot_summary = $_POST["plot-summary"] ?? null;
    $actors = $_POST["actors"] ?? null;

    // genre
    $drama = $_POST["drama"] ?? null;
    $action = $_POST["action"] ?? null;
    $horror = $_POST["horror"] ?? null;
    $comedy = $_POST["comedy"] ?? null;
    $thriller = $_POST["thriller"] ?? null;
    $scifi = $_POST["scifi"] ?? null;
    $romance = $_POST["romance"] ?? null;
    $other = $_POST["other"] ?? null;


    // video type
    $dvd = $_POST["dvd"] ?? null;
    $bluray = $_POST["bluray"] ?? null;
    $four_k = $_POST["4k"] ?? null;
    $digital_sd = $_POST["digital-sd"] ?? null;
    $digital_hd = $_POST["digital-hd"] ?? null;
    $digital_4k = $_POST["digital-4k"] ?? null;

    if (isset($_POST["submit"])) {
        // SANITIZE INPUT
        $cover_url = filter_var($cover_url, FILTER_SANITIZE_URL);
        $title =filter_var($title, FILTER_SANITIZE_STRING);
        $studio = filter_var($studio, FILTER_SANITIZE_STRING);
        $plot_summary = filter_var($plot_summary, FILTER_SANITIZE_STRING);
        $actors = filter_var($actors, FILTER_SANITIZE_STRING);

        // save genre checklist data 
        isset($_POST["drama"]) ?  $drama = 1 : $drama = 0;
        isset($_POST["action"]) ?  $action = 1 : $action = 0;
        isset($_POST["horror"]) ?  $horror = 1 : $horror = 0;
        isset($_POST["comedy"]) ?  $comedy = 1 : $comedy = 0;
        isset($_POST["thriller"]) ?  $thriller = 1 : $thriller = 0;
        isset($_POST["scifi"]) ?  $scifi = 1 : $scifi = 0;
        isset($_POST["romance"]) ?  $romance = 1 : $romance = 0;

        // save video type check list data 
        isset($_POST["dvd"]) ?  $dvd = 1 : $dvd = 0;
        isset($_POST["bluray"]) ?  $bluray = 1 : $bluray = 0;
        isset($_POST["4k"]) ?  $four_k = 1 : $four_k = 0;
        isset($_POST["digital-sd"]) ?  $digital_sd = 1 : $digital_sd = 0;
        isset($_POST["digital-hd"]) ?  $digital_hd = 1 : $digital_hd = 0;
        isset($_POST["digital-4k"]) ?  $digital_4k = 1 : $digital_4k = 0;

        // CHECK IF URL IS USED
        if (strlen($cover_url) > 0){
            $isURL = 1;
        } 
        else {
            $isURL = 0;
        }

        // CHOOSE EITHER URL OR IMAGE UPLOAD TO SAVE
        // primary choice is url
        if ($isURL == 0){
            
            // get movie id to attach to name
            $query = "select max(movie_id) from myvid_movies";
            $stmt = $pdo->query($query);
            $results = $stmt->fetch();
            if (isset($results["max(movie_id)"])) {
                $uniqueID = (int)$results["max(movie_id)"] + 1;
            }
            else {
                $uniqueID = 1;
            }

            // CHECK IMAGE
            $file = "cover-img";                

            if (is_uploaded_file($_FILES[$file]["tmp_name"])) {
                $results = checkErrors($file, 2000000);
                if(strlen($results)>0){
                    $errors["file"] = true;
                }
                else{
                    $path = WEBROOT."www_data/";
                    $fileroot = "cimg";
                    $file = "cover-img";                
                    $newname = createFilename($file, $path, $fileroot, $uniqueID);
                    if(!move_uploaded_file($_FILES[$file]['tmp_name'], $newname)){
                        $errors["move_upload"] = true;
                    }
                }
            }
            else {
                $errors["file"] = true;
            }

        }
        else { 
            // CHECK URL 
            if (strlen($cover_url) > 0) {

                if (getimagesize($cover_url) == false) {
                    $errors["cover_url"] = true;   
                }  
            }
        }
    
        // ERROR CHECK
        if (!isset($cover_url) && !isset($cover_img)) { $errors["cover"] = true;}
        if (!strlen($title)) { $errors["title"] = true; }
        if ($year <= 0 ) { $errors["year"] = true;}
        if ($runtime <= 0) { $errors["runtime"] = true; }
        if (!strlen($studio)) { $errors["studio"] = true;}
        if (!isset($theatrical_release) || strlen($theatrical_release) == 0) { $errors["theatrical_release"] = true;}
        if (!isset($dvd_release) || strlen($dvd_release) == 0) { $errors["dvd_release"] = true; }
        if (!strlen($plot_summary)) { $errors["plot_summary"] = true; }
        if (!strlen($actors)) { $errors["actors"] = true; }

        // VALIDATE CHECKLIST SECTIONS
        if (!isset($drama) && 
        !isset($action) &&
        !isset($horror) &&
        !isset($comedy) &&
        !isset($scifi) &&
        !isset($thriller) &&
        !isset($romance)) {
            $errors["genre"] = true;
        }
        if (!isset($dvd) && 
        !isset($bluray) &&
        !isset($four_k) &&
        !isset($digital_sd) &&
        !isset($digital_hd) &&
        !isset($digital_4k)) {
            $errors["videotype"] = true;
        }    
        if (!count($errors)) {
            // INSERT INTO MYVID_MOVIES DB
            $query = "insert into myvid_movies (user_id, title, rating, mpaa_rating, year, runtime, studio, theatrical_release
            , streaming_release, actors, plot_summary, img_data, isURL, img_url) values (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$_SESSION["id"], $title, $rating, $mpaa_rating, $year, $runtime, $studio, $theatrical_release, $dvd_release, $actors, $plot_summary, $newname, $isURL, $cover_url]);

            // get movie id for other queries
            $stmt = $pdo->prepare("select max(movie_id) from myvid_movies where user_id = ?");  
            $stmt->execute([$_SESSION["id"]]);
            $movie_result = $stmt->fetch();
            $id = $movie_result["max(movie_id)"];

            // INSERT INTO MYVID_GENRE DB 
            $query = ("insert into myvid_genre (movie_id, user_id, movie_title, drama, action, horror, comedy, thriller, scifi, romance) values(?,?,?,?,?,?,?,?,?,?)");
            $stmt = $pdo->prepare($query);
            $stmt->execute([$movie_result["max(movie_id)"], $_SESSION["id"], $title, $drama, $action, $horror, $comedy, $thriller, $scifi, $romance]);

            // INSERT INTO MYVID_TYPE
            $query = ("insert into myvid_type (movie_id, user_id, dvd, 4k, bluray, digital_hd, digital_sd, digital_4k) values(?,?,?,?,?,?,?,?)");
            $pdo->prepare($query)->execute([$movie_result["max(movie_id)"], $_SESSION["id"], $dvd, $four_k, $bluray, $digital_hd, $digital_sd, $digital_4k]);

            header("Location: ./details.php?id=$id");
            exit();
  
        }
    }


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/headsettings.php"; ?>
    <script defer src="scripts/addvid.js"></script>
    <title>Add Video</title>

</head>

<body id="addvideo-body">
      <!--SMALL HEADER FOR SUBPAGE -->
      <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>

    <!-- ADD VIDEO FORM SECTION -->
    <div id="addvideo-section">
        <form id="addvid-form" action="addvid.php" method="post" enctype = "multipart/form-data">
            <!-- UPLOAD COVER PHOTO SECTION -->
            <div class="upload-cover-section">
                <label for="cover">Upload Cover or URL</label>
                <!-- TO DO INCLUDE VALUE -->
                <input type="file" id="cover" name = "cover-img" accept="image/.png, .jpg, .jpeg">
                <p class="error <?=!isset($errors["move_upload"]) && !isset($errors["file"]) ? "hidden" : ""?>">Error uploading file.</p>
                <label for="cover-url">Link Cover</label>
                <input type="url" id="coverlink" name = "cover-url" value = "<?=$cover_url?>">
                <p class="error <?=!isset($errors["cover_url"]) ? "hidden" : ""?>">Invalid Url</p>
            </div>
            <!-- TEXT INPUT SECTION -->
            <div class="details-input-section">
                <!-- VIDEO TITLE -->
                <input id ="title" class="title-input" type="text" name="title" minlength = "1" maxlength = "40" placeholder="Enter Title Here" value="<?=$title?>">

                <button id= "autofill-btn" class="autofill-btn" type = "button" name="autofill">Autofill by Title </button>

                <!-- RATING -->
                <label for="rating">Rating</label>
                <input  type="range" id="rating" min="1" max="5" name="rating" value="<?=$rating?>">

                <!-- GENRE -->
                <label>Genre </label>
                <div id="genre-checklist-section">
                    <label for="drama"><input type="checkbox" id="drama" name="drama" <?=$drama == 1 ? "checked" : ""?>>Drama</label>
                    <label for="action"><input type="checkbox" id="action" name="action" <?=$action == 1 ? "checked" : ""?>>Action</label>
                    <label for="horror"> <input type="checkbox" id="horror" name="horror" <?=$horror == 1 ? "checked" : ""?>>Horror</label>
                    <label for="commedy"><input type="checkbox" id="commedy" name="comedy" <?=$comedy == 1 ? "checked" : ""?>>Comedy</label>
                    <label for="thriller"> <input type="checkbox" id="thriller" name="thriller" <?=$thriller == 1 ? "checked" : ""?>>Thriller</label>
                    <label for="scifi"> <input type="checkbox" id="scifi" name="scifi" <?=$scifi == 1 ? "checked" : ""?>>Sci-fi</label>
                    <label for="romance"> <input type="checkbox" id="romance" name="romance" <?=$romance == 1 ? "checked" : ""?>> Romance</label>
                    <label for="other"> <input type="checkbox" id="other" name="other" <?=$other == 1 ? "checked" : ""?>> Other</label>

                </div>

                <!-- VIDEO TYPE -->
                <label>Video Type: </label>
                <div id="video-type-checklist-section">
                    <label for="dvd"><input type="checkbox" id="dvd" name="dvd" <?=$dvd == 1 ? "checked" : ""?>>DVD</label>
                    <label for="bluray"><input type="checkbox" id="bluray" name="bluray" <?=$bluray == 1 ? "checked" : ""?>>BluRay</label>
                    <label for="4k"><input type="checkbox" id="4k" name="4k" <?=$four_k == 1 ? "checked" : ""?>>4K</label>
                    <label for="digital-sd"><input type="checkbox" id="digital-sd" name="digital-sd" <?=$digital_sd == 1 ? "checked" : ""?>>Digital SD</label>
                    <label for="digital-hd"><input type="checkbox" id="digital-hd" name="digital-hd" <?=$digital_hd == 1 ? "checked" : ""?>>Digital HD</label>
                    <label for="digital-4k"><input type="checkbox" id="digital-4k" name="digital-4k" <?=$digital_4k == 1 ? "checked" : ""?>>Digital 4K</label>
                </div>

                <!-- MPAA RATING -->
                <fieldset>
                    <legend>MPAA Rating</legend>
                    <select name="mpaa-rating" id="mpaa-rating" value="<?=$mpaa_rating?>">
                        <option value="g">General</option>
                        <option value="m">Mature</option>
                        <option value="pg">Parental Guidance</option>
                        <option value="r">Restricted</option>
                        <option value="x">NC-17</option>
                    </select>
                </fieldset>

                <!-- YEAR -->
                <label for="year"> Year </label>
                <input class= "addvid-input" id="year" type="number" min="1600" name="year" value="<?=$year?>">

                <!-- RUN TIME -->
                <label for="runTime">Run Time (mn)</label>
                <input class= "addvid-input" id="runTime" type="number" min="5" name="runTime" value="<?=$runtime?>">

                <!-- STUDIO NAME -->
                <label for="studio">Studio</label>
                <input class= "addvid-input" id="studio" type="text" name="studio" value="<?=$studio?>">

                <!-- THEATRICAL RELEASE -->
                <label for="theatricalRelease">Theatrical Release</label>
                <input class= "addvid-input" id="theatricalRelease" type="date" name="theatrical-release" value="<?=$theatrical_release?>">

                <!-- DVD RELEASE -->
                <label for="dvd-release">DVD / Streaming Release</label>
                <input class= "addvid-input" id="dvd-release" type="date" name="dvd-release" value="<?=$dvd_release?>">

                <!-- ACTORS -->
                <label for="actors">Actors </label>
                <input class= "addvid-input" id="actors" type="text" name="actors" value="<?=$actors?>">

                <!-- PLOT SUMMARY -->
                <label for="plot-summary">Plot Summary</label>
                <textarea class= "addvid-input" id="plot-summary" name="plot-summary" minlength = "10" maxlength="2500"><?=$plot_summary?></textarea>

                <p id = "addvid-error" class="error <?=!count($errors) ? "hidden" : ""?>">Some fields are incomplete.</p>
                <button id="addvid-btn" class="addvid-btn" name="submit" type="submit">Add Video</button>

            </div>

        </form>

    </div>
    <!-- FOOTER -->
    <footer id="addvid-footer">
        &copy; 2022 Kristy Rath
    </footer>


</body>

</html>