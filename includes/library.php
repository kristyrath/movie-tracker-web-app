<?php
// Get the actual document and webroot path for virtual directories
$direx = explode('/', getcwd());
define('DOCROOT', "/$direx[1]/$direx[2]/"); // /home/username/
define('WEBROOT', "/$direx[1]/$direx[2]/$direx[3]/"); // /home/username/public_html

function connectDB() {
  // Load configuration as an array.
  $config = parse_ini_file(DOCROOT."pwd/config.ini");
  $dsn = "mysql:host=$config[domain];dbname=$config[dbname];charset=utf8mb4";

  try {
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
  } catch (\PDOException $e) {
    // In real code, you would *handle* this error, not throw it back up
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
  }

  return $pdo;
}
// selects cover, either use the image or url
function selectCover($row) {
  $img_name = explode("/", $row["img_data"]);
  if ($row["isURL"] != 1 ) {
      $selected_cover = "https://loki.trentu.ca/~kristyrath/www_data/".$img_name[count($img_name)-1];
  }
  else {
      $selected_cover = $row["img_url"];
  }
  return $selected_cover;
}

// checks if session is expired
function sessionCheck() {
  // if not logged in, redirect
  if (!isset($_SESSION["id"])) {
    header("Location: ./login.php");
    exit();
  }
  // if recent active session is over 10 minutes, redirect and stop session
  if (isset($_SESSION['recent']) && (time() - $_SESSION['recent'] > 1200)) {
    session_unset();      
    session_destroy();
    header("Location: ./logout.php");
    exit();    
  }
  $_SESSION['recent'] = time();  
}

// creates a file name
function createFilename($file, $path, $prefix,$uniqueID){
  $filename = $_FILES[$file]['name'];
  $exts = explode(".", $filename);
  $ext = $exts[count($exts)-1];
  $filename = $prefix.$uniqueID.".".$ext;
  $newname=$path.$filename;
  return $newname;
 }
 
  
function checkErrors($file, $limit){
  //modified from http://www.php.net/manual/en/features.file-upload.php
  try{
      // Undefined | Multiple Files | $_FILES Corruption Attack
      // If this request falls under any of them, treat it invalid.
      if(!isset($_FILES[$file]['error']) || is_array($_FILES[$file]['error'])) {
          throw new RuntimeException('Invalid parameters.');
      }
 
      // Check Error value.
      switch ($_FILES[$file]['error']) {
          case UPLOAD_ERR_OK:
              break;
          case UPLOAD_ERR_NO_FILE:
              throw new RuntimeException('No file sent.');
          case UPLOAD_ERR_INI_SIZE:
          case UPLOAD_ERR_FORM_SIZE:
              throw new RuntimeException('Exceeded filesize limit.');
          default:
              throw new RuntimeException('Unknown errors.');
      }
 
      // You should also check filesize here.
      if ($_FILES[$file]['size'] > $limit) {
          throw new RuntimeException('Exceeded filesize limit.');
      }
 
      // Check the File type
      if (exif_imagetype( $_FILES[$file]['tmp_name']) != IMAGETYPE_JPEG
       and exif_imagetype( $_FILES[$file]['tmp_name']) != IMAGETYPE_PNG){
 
           throw new RuntimeException('Invalid file format.');
      }
 
     return "";
 
  } catch (RuntimeException $e) {
 
     return $e->getMessage();
 
  }
}



