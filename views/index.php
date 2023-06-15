<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

$sql = "SELECT * FROM genres ORDER BY genre_id ASC";
$results = $conn->query($sql);

if ($results->num_rows > 0) {
    $sliderNumber = 1;
    while ($rowGenres = $results->fetch_assoc()) {
        $movieRow = goTrhoughMovies($rowGenres, $conn, $tmdb);
        if ( $movieRow != '' ) {
            $genre_slider = 'genre-slider-'.$sliderNumber;

            echo '<div class="row genre-slider '.$genre_slider.'">';
                echo '<div class="col12 column marg-bottom-l">';
                    echo '<div class="column">';
                        echo '<h3>'.$rowGenres['genre_name'].'</h3>';
                    echo '</div>';

                    echo '<div class="column">'; 
                        echo '<div class="swiper card-slider">';
                            echo '<div class="swiper-wrapper">';
                                echo $movieRow;
                            echo '</div>';
                            echo '<div class="swiper-button-prev"></div>
                            <div class="swiper-button-next"></div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
            $sliderNumber++;
        }
    }
} else {
    echo '<p>Keine Genres vorhanden!</p>';
}

function goTrhoughMovies($db_genre, $conn, $tmdb) {
    $movieSQL = "SELECT * FROM movies";
    $resultsMovies = $conn->query($movieSQL);
    $movieRow = '';

    if ($resultsMovies->num_rows > 0) {
        while ($movie = $resultsMovies->fetch_assoc() ) {
            $movieID = $movie['movie_tmdbID'];
            $movieTitle = $movie['movie_title'];
            $movieOverview = $movie['movie_overview'];
            $movieRating = $movie['movie_rating'];
            $movieRuntime = $movie['movie_runtime'];
            $moviePoster = $movie['movie_poster'];
            $movieBackdrop = $movie['movie_thumbnail'];
            $genres = json_decode($movie['movie_genres']);

            foreach ( $genres as $genre ) {
                if ( $db_genre['genre_id'] == $genre ) {
                    $movieRow = $movieRow . '
                    <div class="swiper-slide">
                        <a href="#modal-'.$movieID.'" title="'.$movieTitle.'" class="widescreen-media-card" data-modal data-src="#content-'.$movieID.'">
                            <figure class="widescreen">
                                <img src="'.$tmdb->getImageURL().$movieBackdrop.'" alt="">
                            </figure>
                            <span class="title">'.truncate($movieTitle,20).'</span>
                        </a>

                        <div class="info-popup" id="content-'.$movieID.'" style="display:none;">
                            <div class="row">
                                <div class="col8">
                                    <p class="h4">'.$movieTitle.'</p>
                                    <p>'.$movieOverview.'</p>
                                    <div class="col6">
                                        <span><strong>Bewertung:</strong><br>'.$movieRating.'/10</span>
                                    </div>
                                    <div class="col6">
                                        <span><strong>Dauer:</strong><br>'.runtimeToString($movieRuntime).'</span>
                                    </div>
                                </div>
                                <div class="col4">
                                    <figure class="poster">
                                        <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>'; 
                }
            }
        }
    }

    return $movieRow;
}

include(ROOT_PATH.'/views/footer.php');
?>