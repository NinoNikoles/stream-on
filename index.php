<?php 

ini_set('max_execution_time', 300);
$session_duration = 60 * 60 * 24 * 7;
ini_set('session.cookie_lifetime', $session_duration);
define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
require_once ROOT_PATH.'/src/admin/init.php';

init();

?>