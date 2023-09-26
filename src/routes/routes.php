<?php

if ( loggedInCheck() ) {
    routes('public');
} else {
    routes('login');
}

routes('essentials');

/////////////////////////////////////////////////////////////////////////////////////////////////
//-- AJAX STUFF --
routes('ajax');

/////////////////////////////////////////////////////////////////////////////////////////////////
// Admin Check
if ( loggedInCheck() ) {
    if ( $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin' ) {
        routes('admin');
    }
}

routes('error');

$folderPath = 'css/';

$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($folderPath), RecursiveIteratorIterator::SELF_FIRST);

foreach ($iterator as $file) {
    if ($file->isFile()) {
        $filePath = str_replace("\\", "/", $file->getPathname());

        get('/'.$filePath, $filePath);
        post('/'.$filePath, $filePath);
    }
}