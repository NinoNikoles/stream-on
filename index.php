<?php 

define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
require_once ROOT_PATH.'/config.php';
require_once ROOT_PATH."/src/admin/functions.php";
require_once ROOT_PATH.'/src/admin/config.php';
require_once ROOT_PATH.'/src/routes/router.php';

//---------------------------------------------------
require_once ROOT_PATH.'/src/routes/routes.php';
//---------------------------------------------------
?>