<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

echo '<div class="innerWrap marg-top-l marg-bottom-l">';
    echo '<div class="grid-row" id="movie-list">';
    echo '</div>';
    echo '<span id="load-count" style="display:none;" class="visible">0</span>';
    echo '<div id="loader" class="active"></div>';
echo '</div>';

include(ROOT_PATH.'/views/footer.php');
?>