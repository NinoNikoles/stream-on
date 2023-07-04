<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();


currentWatchlist();

genreSlider();

include(ROOT_PATH.'/views/footer.php');
?>