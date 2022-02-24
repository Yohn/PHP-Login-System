<?php
// Start a Session
if (!session_id()) @session_start();
require_once('E:/php-guestbook.com/github/PHP-Login-System/assets/setup/env.php');

//
//
// Production
//error_reporting(E_ALL & ~E_DEPRECATED & ~E_STRICT);
//ini_set('display_errors', 0);
//
// Development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
//
//
require_once(APP_PATHS.'/assets/setup/db.inc.php');
require_once(APP_PATHS.'/assets/includes/auth_functions.php');
require_once(APP_PATHS.'/assets/includes/security_functions.php');
require_once(APP_PATHS.'/assets/vendor/usersonline/Master.class.php');
$ol=new Master();
$dbh=$ol->dbh();
 ?>
