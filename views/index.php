<?php
include(ROOT_PATH.'/views/head.php');
include(ROOT_PATH.'/views/header.php');

//-- Main Content --
?>

<?php
$genreSQL = "SELECT * FROM genres ORDER BY genre_id ASC";
$results = $conn->query($genreSQL);


function goTrhoughMovies($tmdb, $conn, $db_genre) {
    $movieSQL = "SELECT * FROM movies";
    $resultsMovies = $conn->query($movieSQL);
    $movieRow = '';

    if ($resultsMovies->num_rows > 0) {
        while ($movie = $resultsMovies->fetch_assoc() ) {
            $movieID = $movie['movie_tmdbID'];
            $movieTitle = $movie['movie_title'];
            $moviePoster = $movie['movie_poster'];
            $genres = json_decode($movie['movie_genres']);

            foreach ( $genres as $genre ) {
                if ( $db_genre['genre_id'] == $genre ) {
                    $movieRow = $movieRow . '
                    <div class="swiper-slide">
                        <a href="/movies/edit-movie/?id='.$movieID.'" title="'.$movieTitle.'" class="media-card">
                            <figure class="poster">
                                <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">
                            </figure>
                            <span class="title">'.truncate($movieTitle,20).'</span>
                        </a>
                    </div>'; 
                }
            }
        }
    }

    return $movieRow;
}

if ($results->num_rows > 0) {
    while ($rowGenres = $results->fetch_assoc()) {
        $movieRow = goTrhoughMovies($tmdb, $conn, $rowGenres);
        if ( $movieRow != '' ) {
            echo '<div class="row">';
                echo '<div class="col12 column margin-bottom-l">';
                    echo '<div class="column">';
                        echo '<h3>'.$rowGenres['genre_name'].'</h3>';
                    echo '</div>';
                        
                    echo '<div class="swiper card-slider">';
                        echo '<div class="swiper-wrapper">';
                            echo $movieRow;
                        echo '</div>';
                        echo '<div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        }
    }
}
?>




<?php
//------------------
include(ROOT_PATH.'/views/footer.php');
?>