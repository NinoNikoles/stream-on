<?php 
$pageTitle = pageTitle('');
include(ROOT_PATH.'/views/header.php');

getHighlight();

currentWatchlist();

genreSlider();

include(ROOT_PATH.'/views/footer.php');
?>