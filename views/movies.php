<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

echo '<span class="load-count visible" style="display: none;">0</span>';
echo '<div class="innerWrap marg-top-l" id="movie-list">';
    /*echo '<div class="row" >';
    echo '</div>';*/
echo '</div>';

include(ROOT_PATH.'/views/footer.php');
?>