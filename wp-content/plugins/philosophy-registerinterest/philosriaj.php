<?php
session_start();
$filename = basename($_SERVER['SCRIPT_FILENAME']);
$t[] = microtime(true);
include( $_SERVER['DOCUMENT_ROOT']. '/wp-load.php' );

define("REGINT_LOC", plugin_dir_path(__FILE__));
define("REGINT_URI", plugin_dir_url(__FILE__));
define("REGINT_DATEFORMAT", 'd/m/Y @ H:i');

##### The Includes
/* . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . . */
require_once REGINT_LOC."inc/common.php";
require_once REGINT_LOC."inc/class_RegisterInterest.php";
require_once REGINT_LOC."inc/class_Browser.php";
require_once REGINT_LOC."inc/PHPMailer-master/PHPMailerAutoload.php";

PHILOSRI_handleSubmit();