<?php
require_once ROOT_PATH.'/src/language/language.php';
require_once ROOT_PATH.'/src/admin/routing.php';
require_once ROOT_PATH.'/src/admin/modules/universal.php';
include_modules('movies');
include_modules('shows');
include_modules('genre');
include_modules('user');
include_modules('api');
include_modules('player');
include_modules('container');
include_modules('trailer');



