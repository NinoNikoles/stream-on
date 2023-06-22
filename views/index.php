<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

$sql = "SELECT * FROM genres ORDER BY genre_id ASC";
$results = $conn->query($sql);
var_dump($_SESSION);

if ($results->num_rows > 0) {
    $sliderNumber = 1;

    while ($genre = $results->fetch_assoc()) {
        $movieRow = goTrhoughMovies($genre['id'], $conn, $tmdb);
        if ( $movieRow != '' ) {
            $genre_slider = 'genre-slider-'.$sliderNumber;

            echo '<div class="genre-slider '.$genre_slider.'">';
                echo '<div class="col12 marg-top-l">';
                    echo '<div class="column">';
                        echo '<h3>'.$genre['genre_name'].'</h3>';
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
    echo '<div class="innerWrap marg-top-l">';
        echo '<div class="col12">';
            echo '<p>Keine Genres vorhanden!</p>';
        echo '</div>';
    echo '</div>';
}

function goTrhoughMovies($db_genre, $conn, $tmdb) {
    $query = "SELECT * FROM movies INNER JOIN movie_genre ON movies.movie_tmdbID = movie_genre.movie_id WHERE movie_genre.genre_id = $db_genre";
    $result = $conn->query($query);
    $movieRow = '';

    if ($result->num_rows > 0) {
        // Es gibt mindestens einen Film des Genres
    
        while ($movie = $result->fetch_assoc()) {
            $movieID = $movie['movie_tmdbID'];
            $movieTitle = $movie['movie_title'];
            $movieOverview = $movie['movie_overview'];
            $movieRating = $movie['movie_rating'];
            $movieRuntime = $movie['movie_runtime'];
            $movieRelease = new DateTime($movie['movie_release']);
            $releaseYear = $movieRelease->format('Y');
            $moviePoster = $movie['movie_poster'];
            $movieBackdrop = $movie['movie_thumbnail'];
            $genres = json_decode($movie['movie_genres']);
            $genreHTML = '';
            foreach ( $genres as $genre ) {
                $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
            }

            $movieRow = $movieRow . '
            <div class="swiper-slide">
                <div class="media-card widescreen-media-card desktop-only">
                    <figure class="widescreen">
                        <img src="'.$tmdb->getImageURL().$movieBackdrop.'" alt="">
                    </figure>
                    <div class="link-wrapper">
                        <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                        <a href="#content-'.$movieID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                    </div>
                </div>

                <div class="media-card mobile-only">
                    <figure class="poster">
                        <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">
                    </figure>
                    <div class="link-wrapper">
                        <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                        <a href="#content-'.$movieID.'" title="'.$movieTitle.'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                    </div>
                </div>

                <div class="info-popup" id="content-'.$movieID.'" style="display:none;">
                    <div class="col12 marg-bottom-xs mobile-only">
                        <figure class="widescreen">
                            <img src="'.$tmdb->getImageURL().$movieBackdrop.'">
                        </figure>
                    </div>
                    <div class="innerWrap">
                        <div class="col7 marg-right-col1">
                            <p class="h2">'.$movieTitle.'</p>
                            <p class="small">
                                <span class="tag">'.$releaseYear.'</span>
                                <span class="tag">'.$movieRating.'/10</span>
                                <span class="tag">'.runtimeToString($movieRuntime).'</span>
                            </p>
                            <a href="/watch/?id='.$movieID.'" class="btn btn-white icon-left icon-play">Jetzt schauen</a>
                            <p class="small">'.$movieOverview.'</p>
                            <p class="small">'.$genreHTML.'</p>
                        </div>
                        <div class="col4 desktop-only">
                            <figure class="poster">
                                <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">
                            </figure>
                        </div>
                    </div>
                </div>
            </div>';
        }
    } else {
        $movieRow = '';
    }

    return $movieRow;
}
echo '<div class="marg-bottom-l"></div>';

include(ROOT_PATH.'/views/footer.php');
?>