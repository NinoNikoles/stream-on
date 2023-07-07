<?php 
include(ROOT_PATH.'/views/head.php');

echo '<style>body { padding-top:0!important; }</style>';
echo '<div id="mainPlayer" class="fullscreen">';
    videoPlayer($_GET['id'], true);
echo '</div>';

include('views/footer.php'); ?>