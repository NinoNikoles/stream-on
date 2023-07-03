<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();


currentWatchlist();


$sql = "SELECT * FROM genres ORDER BY genre_id ASC";
$results = $conn->query($sql);

if ($results->num_rows > 0) {
    $sliderNumber = 1;

    while ($genre = $results->fetch_assoc()) {
        $movieRow = goTrhoughMovies($genre['genre_id'], $conn, $tmdb);
        
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
    $genreID = intval($db_genre);
    
    $query = "SELECT * FROM movies INNER JOIN movie_genre ON movies.movie_tmdbID = movie_genre.movie_id WHERE movie_genre.genre_id = $genreID ORDER BY RAND() LIMIT 20";
    $result = $conn->query($query);
    
    $movieRow = '';

    if ($result->num_rows > 0) {
        // Es gibt mindestens einen Film des Genres
    
        while ($movie = $result->fetch_assoc()) {
            $movieRow = $movieRow . movie_card($movie, 'swiper-slide');  
        }
    } else {
        $movieRow = '';
    }

    return $movieRow;
}
echo '<div class="marg-bottom-l"></div>';

include(ROOT_PATH.'/views/footer.php');
?>