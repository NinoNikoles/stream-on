<?php
    include(ROOT_PATH.'/views/head.php');
    include(ROOT_PATH.'/views/header.php');


$genres = $tmdb->getGenres();
$i = 1;
foreach ($genres as $genre) {
    echo '<p>'. $i . '. ' . $genre->getName() . '</p>';
    $i++;
}

include(ROOT_PATH.'/views/footer.php'); ?>