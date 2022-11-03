<!-- 
COIS 3420 As1
NAME:           Kristy Rath (0707345)
PAGE NAME:      Edit Video
DESCRIPTION:    Allows user to edit  videos and corresponding information into their collection.
HTML CODE SECTIONS:
    1. small header
    2. nav bar and quick access bar
    4. add video form
    5. footer -->


    
    <?php 

// CONNECT TO DB AND SESSIONS
require_once("includes/library.php");
$pdo = connectDB();
session_start();
sessionCheck();

$errors = array();
$results = array();

// VARIABLE DECLARATION
isset($_POST["keep_prev"]) ?  $keep_prev = 1 : $keep_prev = 0;
$cover_img_uploaded = false ?? null;
$cover_url = $_POST["cover-url"] ?? null;
$newname = null;
$title = $_POST["title"] ?? null;
$rating = $_POST["rating"] ?? null;
$year = $_POST["year"] ?? null;
$mpaa_rating = $_POST["mpaa-rating"] ?? null;
$runtime = $_POST["runTime"] ?? null;
$studio = $_POST["studio"] ?? null;
$theatrical_release = $_POST["theatrical-release"] ?? null;
$dvd_release = $_POST["dvd-release"] ?? null;
$plot_summary = $_POST["plot-summary"] ?? null;
$actors = $_POST["actors"] ?? null;


// CHECK IF USER OWN VIDEO
$uri = $_SERVER['REQUEST_URI'];
$uri = filter_var($uri, FILTER_SANITIZE_URL);
if (filter_var($uri, FILTER_VALIDATE_URL)) {
    $errors["uri"] = true;
    header("Location: ./index.php");
    exit();
}
else {
    // GET OF VIDEO FROM URL
    $var = parse_url($uri);
    $str = explode("=",$var["query"]);
    $movie_id = $str[1];
    $id = $_SESSION["id"];
    
    // SELECT VIDEO
    $query = "select * from myvid_movies where movie_id = ? and user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$movie_id, $id]);
    $movie_info = $stmt->fetch();

    // IF MOVIE DOES NOT EXIST, REDIRECT TO MAIN
    if (!($movie_info)) {
        header("Location: ./index.php");
        exit();
    }
    
    // SELECT MOVIE GENRE INFO FROM DATABASE
    $query = "select * from myvid_genre where movie_id = ? and user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$movie_id, $id]);
    $genres = $stmt->fetch();

    // SELECT MOVIE TYPE INFO FROM DATABASE
    $query = "select * from myvid_type where movie_id = ? and user_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$movie_id, $id]);
    $type = $stmt->fetch();

    // SELECT COVER TO DISPLAY  
    $img_name = explode("/", $movie_info["img_data"]);
    if ($movie_info["isURL"]==0 ) {
        $selected_cover = "https://loki.trentu.ca/~kristyrath/www_data/".$img_name[count($img_name)-1];
    }
    else {
        $selected_cover = $movie_info["img_url"];
    }
}
// CHECK NEW INPUT
if (count($errors) == 0) {
    $correct = true;
}
if (isset($_POST["submit"])) {

    // VALIDATE AND SANITIZE INPUT
    $cover_url = filter_var($cover_url, FILTER_SANITIZE_URL);
    $title =filter_var($title, FILTER_SANITIZE_STRING);
    $studio = filter_var($studio, FILTER_SANITIZE_STRING);
    $plot_summary = filter_var($plot_summary, FILTER_SANITIZE_STRING);
    $actors = filter_var($actors, FILTER_SANITIZE_STRING);
    
    // SAVE GENRE CHECKLIST
    isset($_POST["drama"]) ?  $drama = 1 : $drama = 0;
    isset($_POST["action"]) ?  $action = 1 : $action = 0;
    isset($_POST["horror"]) ?  $horror = 1 : $horror = 0;
    isset($_POST["comedy"]) ?  $comedy = 1 : $comedy = 0;
    isset($_POST["thriller"]) ?  $thriller = 1 : $thriller = 0;
    isset($_POST["scifi"]) ?  $scifi = 1 : $scifi = 0;
    isset($_POST["romance"]) ?  $romance = 1 : $romance = 0;

    // SAVE VIDEO TYPE CHECKLIST
    isset($_POST["dvd"]) ?  $dvd = 1 : $dvd = 0;
    isset($_POST["bluray"]) ?  $bluray = 1 : $bluray = 0;
    isset($_POST["4k"]) ?  $four_k = 1 : $four_k = 0;
    isset($_POST["digital-sd"]) ?  $digital_sd = 1 : $digital_sd = 0;
    isset($_POST["digital-hd"]) ?  $digital_hd = 1 : $digital_hd = 0;
    isset($_POST["digital-4k"]) ?  $digital_4k = 1 : $digital_4k = 0;

    // CHECK IF URL IS USED OR IMAGE UPLOAD
    if (strlen($cover_url) != 0){
        $isURL = 1;
    } 
    else {
        $isURL = 0;
    }

    // IF NEW IMAGE IS UPLOADED
    if ($keep_prev == 0) {
        // IF NEW UPLOAD IS URL
        if ($isURL == 0){
            // CHECK IMAGE
            $file = "cover-img";                
            // validate if file is uploaded
            if (is_uploaded_file($_FILES[$file]["tmp_name"])) {
                // check for errors
                $results = checkErrors($file, 2000000);
                if(strlen($results)>0){
                    $errors["file"] = true;
                }
                else{
                    // assign path name to store in db
                    $path = WEBROOT."www_data/";
                    $fileroot = "cimg-".microtime(true);
                    $newname = createFilename($file, $path, $fileroot, $movie_info["movie_id"]);
                    // move file to db
                    if(!move_uploaded_file($_FILES[$file]['tmp_name'], $newname)){
                        $errors["move_upload"] = true;
                    }
                    else {
                        $cover_url = null;
                        $cover_img_uploaded = true;

                    }
                }
            }
            else {
                    $errors["file"] = true;
            }

        }
        else { 
            // CHECK URL 
            if (isset($cover_url)) {
                // check if url contains image
                if (getimagesize($cover_url) == false) {
                    $errors["cover_url"] = true;   
                }  
            }
        }

    }
    // CHECK IF OTHER FIELDS IS FILLED
    if (!isset($cover_url) && !$cover_img_uploaded) { $errors["cover"] = true;}
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
        $query = "update myvid_movies set  title = ?, rating = ?, mpaa_rating = ?, year = ?, runtime = ?, studio = ?, theatrical_release = ?
        , streaming_release = ?, actors = ?, plot_summary = ? where movie_id = ? and user_id = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$title, $rating, $mpaa_rating, $year, $runtime, $studio, $theatrical_release, $dvd_release, $actors, $plot_summary, $movie_info["movie_id"], $movie_info["user_id"]]);

        // UPDATE COVER IF USER UPLOADS NEW
        if ($keep_prev == 0) {
            $query = "update myvid_movies set img_data = ?, isURL = ?, img_url = ? where movie_id = ? and user_id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$newname, $isURL, $cover_url, $movie_info["movie_id"], $movie_info["user_id"]]);
        }

        // INSERT INTO MYVID_GENRE DB 
        $query = ("update myvid_genre set movie_title = ?, drama = ?, action = ?, horror = ?, comedy = ?, thriller = ?, scifi = ?, romance = ? where movie_id = ?");
        $pdo->prepare($query)->execute([$title, $drama, $action, $horror, $comedy, $thriller, $scifi, $romance, $movie_info["movie_id"]]);

        // INSERT INTO MYVID_TYPE
        $query = ("update myvid_type set dvd = ?, 4k = ?, bluray = ?, digital_hd = ?, digital_sd = ?, digital_4k = ? where movie_id = ?");
        $pdo->prepare($query)->execute([$movie_info["movie_id"], $_SESSION["id"], $dvd, $four_k, $bluray, $digital_hd, $digital_sd, $digital_4k, $movie_info["movie_id"]]);

        header("Location: ./details.php?id=$id");
        exit();

    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include "includes/headsettings.php"; ?>
    <title>Edit Video</title>

</head>

<body id="addvideo-body">
      <!-- SMALL HEADER FOR SUBPAGE -->
      <?php include "includes/small-header.php"; ?>
    <!-- NAV & QUICK ACCESS BAR  -->
    <?php include "includes/nav-quickaccessbar.php"; ?>

    <!-- ADD VIDEO FORM SECTION -->
    <div id="addvideo-section">
        <form action="editvid.php?id=<?=$movie_id?>" method="post" enctype = "multipart/form-data">
            <!-- UPLOAD COVER PHOTO SECTION -->
            <div class="upload-cover-section">
                <style>
                    .upload-cover-section{
                        background-image: url("<?=$selected_cover?>");
                    }
                </style>
                <!-- TO DO INCLUDE VALUE -->

                <input type="file"  id="cover" name = "cover-img" accept="image/.png, .jpg, .jpeg">

                <p class="error <?=!isset($errors["move_upload"]) && !isset($errors["file"]) ? "hidden" : ""?>">Error uploading file.</p>
                <input type="url" id="coverlink" name = "cover-url" placeholder="Link Cover" value="<?=$movie_info["img_url"]?>">


                <div>
                    <input id = "inline"  type="checkbox" name="keep_prev" checked>
                    <label for = "keep_prev"> Use Previous Cover</label>
                </div>
                <p class="error <?=!isset($errors["cover_url"]) ? "hidden" : ""?>">Invalid Url</p>
            </div>
            <!-- TEXT INPUT SECTION -->
            <div class="details-input-section">
                <!-- VIDEO TITLE -->
                <input class="title-input" type="text" name="title" minlength = "1" maxlength = "40" placeholder="Enter Title Here" value="<?=$movie_info["title"]?>">

                <!-- RATING -->
                <label for="rating">Rating</label>
                <input type="range" id="rating" min="1" max="5" name="rating" value="<?=$movie_info["rating"]?>">

                <!-- GENRE -->
                <label>Genre </label>
                <div id="genre-checklist-section">
                    <label for="drama"><input type="checkbox" id="drama" name="drama" <?=$genres["drama"] == 1 ? "checked" : ""?>>Drama</label>
                    <label for="action"><input type="checkbox" id="action" name="action" <?=$genres["action"] == 1 ? "checked" : ""?>>Action</label>
                    <label for="horror"> <input type="checkbox" id="horror" name="horror" <?=$genres["horror"] == 1 ? "checked" : ""?>>Horror</label>
                    <label for="commedy"><input type="checkbox" id="commedy" name="comedy" <?=$genres["comedy"] == 1 ? "checked" : ""?>>Commedy</label>
                    <label for="thriller"> <input type="checkbox" id="thriller" name="thriller" <?=$genres["thriller"] == 1 ? "checked" : ""?>>Thriller</label>
                    <label for="scifi"> <input type="checkbox" id="scifi" name="scifi" <?=$genres["scifi"] == 1 ? "checked" : ""?>>Sci-fi</label>
                    <label for="romance"> <input type="checkbox" id="romance" name="romance" <?=$genres["romance"] == 1 ? "checked" : ""?>> Romance</label>
                </div>

                <!-- VIDEO TYPE -->
                <label>Video Type: </label>
                <div id="video-type-checklist-section">
                    <label for="dvd"><input type="checkbox" id="dvd" name="dvd" <?=$type["dvd"] == 1 ? "checked" : ""?>>DVD</label>
                    <label for="bluray"><input type="checkbox" id="bluray" name="bluray" <?=$type["bluray"] == 1 ? "checked" : ""?>>BluRay</label>
                    <label for="4k"><input type="checkbox" id="4k" name="4k" <?=$type["4k"] == 1 ? "checked" : ""?>>4K</label>
                    <label for="digital-sd"><input type="checkbox" id="digital-sd" name="digital-sd" <?=$type["digital_sd"] == 1 ? "checked" : ""?>>Digital SD</label>
                    <label for="digital-hd"><input type="checkbox" id="digital-hd" name="digital-hd" <?=$type["digital_hd"] == 1 ? "checked" : ""?>>Digital HD</label>
                    <label for="digital-4k"><input type="checkbox" id="digital-4k" name="digital-4k" <?=$type["digital_4k"] == 1 ? "checked" : ""?>>Digital 4K</label>
                </div>

                <!-- MPAA RATING -->
                <fieldset>
                    <legend>MPAA Rating</legend>
                    <select name="mpaa-rating" id="mpaa-rating">
                        <option value="g" <?=$movie_info["mpaa_rating"] = "g" ? "selected=\"selected\"" : ""?>>General</option>
                        <option value="m" <?=$movie_info["mpaa_rating"] = "m" ? "selected=\"selected\"" : ""?>>Mature</option>
                        <option value="pg" <?=$movie_info["mpaa_rating"] = "pg" ? "selected=\"selected\"" : ""?>>Parental Guidance</option>
                        <option value="r" <?=$movie_info["mpaa_rating"] = "r" ? "selected=\"selected\"" : ""?>>Restricted</option>
                        <option value="x" <?=$movie_info["mpaa_rating"] = "x" ? "selected=\"selected\"" : ""?>>NC-17</option>
                    </select>
                </fieldset>

                <!-- YEAR -->
                <label for="year"> Year </label>
                <input id="year" type="number" min="1600" max="2022" name="year" value="<?=$movie_info["year"]?>">

                <!-- RUN TIME -->
                <label for="runTime">Run Time (mn)</label>
                <input id="runTime" type="number" min="5" name="runTime" value="<?=$movie_info["runtime"] ?>">

                <!-- STUDIO NAME -->
                <label for="studio">Studio</label>
                <input id="studio" type="text" name="studio" value="<?=$movie_info["studio"]?>">

                <!-- THEATRICAL RELEASE -->
                <label for="theatricalRelease">Theatrical Release</label>
                <input id="theatricalRelease" type="date" name="theatrical-release" value="<?=$movie_info["theatrical_release"]?>">

                <!-- DVD RELEASE -->
                <label for="dvd-release">DVD / Streaming Release</label>
                <input id="dvd-release" type="date" name="dvd-release" value="<?=$movie_info["streaming_release"]?>">

                <!-- ACTORS -->
                <label for="actors">Actors </label>
                <input id="actors" type="text" name="actors" value="<?=$movie_info["actors"]?>">

                <!-- PLOT SUMMARY -->
                <label for="plot-summary">Plot Summary</label>
                <textarea id="plot-summary" name="plot-summary" minlength = "10" maxlength="300"><?=$movie_info["plot_summary"]?></textarea>

                <p class="error <?=$correct == true ? "hidden" : "" ?>">Some fields are incomplete.</p>
                <button class="addvid-btn" name="submit" type="submit">Save Changes</button>

            </div>

        </form>

    </div>
    <!-- FOOTER -->
    <footer id="addvid-footer">
        &copy; 2022 Kristy Rath
    </footer>


</body>

</html>