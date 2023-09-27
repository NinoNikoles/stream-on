<?php 

ini_set('max_execution_time', 300);
$session_duration = 60 * 60 * 24 * 365;
ini_set('session.cookie_lifetime', $session_duration);

if (session_status() === PHP_SESSION_NONE) {
    // Die Session wurde nicht gestartet, also starte sie neu.
    session_start();
}

define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
require_once ROOT_PATH.'/src/admin/init.php';

init();

?>