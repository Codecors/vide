<?php 
/**
 * error reporting level
 */
error_reporting(E_ALL);

/**
 * db access data
 */
define('DB_HOST', 'localhost');
define('DB_NAME', 'video_cms');
define('DB_USER', 'root');
define('DB_PASS', '');

/**
 * root directory path
 */
$root_dir = realpath(__DIR__ . '/../');
$root_dir = $root_dir . '/';
define('ROOT', $root_dir);

/**
 * video file folder path
 */
$vid_path = ROOT . 'www/lib/videos/';
define('VIDEOS_DIR', $vid_path);

/**
 * thumbnail img file folder path
 */
$thumbnail_path = ROOT . 'www/lib/thumbnails/';
define('THUMBS_DIR', $thumbnail_path);

//contact form email address
define("CONTACT_EMAIL", "test@test.de");

//error log file
$error_log_path = ROOT . 'logs/error_log.txt';
define("ERROR_LOG", $error_log_path);

//contact form message log file
$contact_log_path = ROOT . 'logs/contactform_log.txt';
define("CONTACT_LOG", $contact_log_path);

//form token
define("FORM_TOKEN", "0c2d7970ed846b6596925b6ce2dd5b9c");

//session auth salt
define("AUTH_SALT", "fbb5058c47da856b6501160ad0753bba");
