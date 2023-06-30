<?php 

define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);
require_once ROOT_PATH.'/src/admin/setup.php';

configCheck();

require_once ROOT_PATH.'/src/admin/functions.php';
require_once ROOT_PATH.'/src/routes/router.php';

init();

?>