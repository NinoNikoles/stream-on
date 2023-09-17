<?php 
$pageTitle = pageTitle(lang_snippet(('movies')));
include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

echo '<div class="innerWrap marg-top-l marg-bottom-l">';
    echo '<h1>'.lang_snippet('movies').'</h1>';

    // Filter
    include_filter('movie');

    // Media Output
    echo '<div class="grid-row" id="media-list">';
        $movies = selectAllMoviesByTitle('ASC');
        foreach ($movies as $movie) {
            echo media_card($movie, 'col-6 col-4-xsmall col-3-medium grid-padding');
        }
    echo '</div>';
echo '</div>';

include(ROOT_PATH.'/views/footer.php');
?>