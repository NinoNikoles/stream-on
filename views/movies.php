<?php 
$pageTitle = pageTitle(lang_snippet(('movies')));
include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

echo '<div class="innerWrap marg-top-l marg-bottom-l">';
    echo '<h1>'.lang_snippet('movies').'</h1>';

    // Sorting
    echo '<div class="grid-row">';
        echo '<div class="col-12 col-3-medium grid-padding marg-bottom-s">';
            echo '<label class="select">'.lang_snippet('genres');
                echo '<select id="genre-filter">';
                    echo '<option value="all">'.lang_snippet('all').'</option>';

                    $genres = getAllGenre();
                    foreach ( $genres as $genre ) {
                        echo '<option value="'.$genre['genre_id'].'">'.$genre['genre_name'].'</option>';
                    }
                echo '</select>';
            echo '</label>';
            echo '<span id="type-filter" data-type="movie" style="display:none;">';
        echo '</div>';
        echo '<div class="col-12 col-3-medium marg-left-col6 grid-padding marg-bottom-s">';
            echo '<label class="select">'.lang_snippet('sorting');
                echo '<select id="title-filter">';
                    echo '<option value="title ASC">A - Z</option>';
                    echo '<option value="title DESC">Z - A</option>';
                    echo '<option value="releaseDate DESC">Neuste - Älteste</option>';
                    echo '<option value="releaseDate ASC">Älteste - Neuste</option>';
                    echo '<option value="rating DESC">Bewertung: Höchste - Niedrigste</option>';
                    echo '<option value="rating ASC">Bewertung: Niedrigste - Höchste</option>';
                echo '</select>';
            echo '</label>';
        echo '</div>';
    echo '</div>';

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