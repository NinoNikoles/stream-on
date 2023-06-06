<?php 
include(ROOT_PATH.'/views/head.php');

echo '<div class="fullscreen">';
    videoPlayer($_GET['id'], true);
echo '</div>';

include('views/footer.php'); ?>