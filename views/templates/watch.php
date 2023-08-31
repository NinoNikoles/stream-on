<?php 

if ( !isset($_GET['s']) ) {
    $pageTitle = pageTitle(getMediaTitle($_GET['id']));
} else {
    $pageTitle = pageTitle(getMediaTitle($_GET['s']));
}

include(ROOT_PATH.'/views/head.php');

echo '<style>body { padding-top:0!important; }</style>';
echo '<div id="mainPlayer" class="fullscreen">';
    if ( !isset($_GET['s']) ) {
        movieVideoPlayer($_GET['id'], true);
    } else {
        showVideoPlayer($_GET['id'], $_GET['s'], true);
    }    
echo '</div>';

include('views/footer.php'); ?>