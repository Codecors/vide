<?php 
/**
 * loads config file and class files
 */

require_once('config.php');

$path = ROOT . "php/classes/";

include_once($path . 'main_class.php');
include_once($path . 'db_class.php');
include_once($path . 'video_class.php');
include_once($path . 'user_class.php');
include_once($path . 'upload_class.php');
include_once($path . 'email_class.php');