<?php 
include(ROOT_PATH.'/views/head.php');

echo '<div id="mainPlayer" class="fullscreen">';
    videoPlayer($_GET['id'], true);
echo '</div>';

include('views/footer.php'); ?>