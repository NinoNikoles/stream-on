<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

echo '<div class="innerWrap marg-top-l marg-bottom-l">';
    echo '<div class="grid-row" id="movie-list">';
    echo '</div>';
    echo '<p id="loading-message" style="display: none;">Loading...</p>';
echo '</div>';

include(ROOT_PATH.'/views/footer.php');
?>