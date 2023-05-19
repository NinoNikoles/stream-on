<?php 
include(ROOT_PATH.'/views/head.php');

echo '<div class="fullscreen">';
output_movie($_GET['id'], true);
echo '</div>';

include('views/footer.php'); ?>