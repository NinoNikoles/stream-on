<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

echo '<div class="innerWrap marg-top-l marg-bottom-l">';
    echo '<h1>'.lang_snippet('movies').'</h1>';
    echo '<div class="grid-row" id="movie-list">';
    $movies = selectAllMoviesByTitle('ASC');
    foreach ($movies as $movie) {
        echo media_card($movie, 'col-6 col-4-xsmall col-3-medium grid-padding');
    }
    echo '</div>';
echo '</div>';

include(ROOT_PATH.'/views/footer.php');
?>