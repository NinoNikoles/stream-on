<?php

function currentWatchlist() {
    $conn = dbConnect();
    $userID = $_SESSION['userID'];

    $query = "SELECT *
    FROM media
    INNER JOIN media_watched ON media.tmdbID = media_watched.show_id
    WHERE media_watched.user_id = $userID AND media_watched.watched_seconds > 0 AND media_watched.watched != 1
    AND (media_watched.show_id, media_watched.last_watched) IN (
        SELECT show_id, MAX(last_watched)
        FROM media_watched
        GROUP BY show_id
    );";
    $results = $conn->query($query);
    
    if ( $results->num_rows > 0 ) {
        echo '<div class="currentWatch-slider">';
            echo '<div class="col12 marg-top-l">';
                echo '<div class="column">';
                    echo '<h3>'.lang_snippet('continue').'</h3>';
                echo '</div>';

                echo '<div class="column">'; 
                    echo '<div class="swiper card-slider">';
                        echo '<div class="swiper-wrapper">';

                        while ( $movie = $results->fetch_assoc() ) {
                            echo media_card($movie, 'swiper-slide');
                        }
                        echo '</div>';
                        echo '<div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }

    $conn->close();
}

function myList() {
    $conn = dbConnect();
    $userID = $_SESSION['userID'];

    $query = "SELECT * FROM media INNER JOIN watchlist ON media.tmdbID = watchlist.media_id WHERE watchlist.user_id = $userID ORDER BY title ASC";
    $results = $conn->query($query);
    
    if ( $results->num_rows > 0 ) {    
        while ($movies = $results->fetch_assoc()) {
            echo media_card($movies, 'col-6 col-4-xsmall col-3-medium grid-padding');
        }
    }

    $conn->close();
}

function genreSlider() {
    $conn = dbConnect();
    $tmdb = setupTMDB(); // 28, 12, 16, 80, 18, 878, 53

    $sql = "SELECT DISTINCT genres.genre_id, genres.genre_name
    FROM genres WHERE genres.genre_id IN (
        SELECT DISTINCT media_genre.genre_id
        FROM media_genre
    )";
    $results = $conn->query($sql);

    if ($results->num_rows > 0) {
        $sliderNumber = 1;

        while ($genre = $results->fetch_assoc()) {
            $mediaRow = goTrhoughMedia($genre['genre_id'], $conn, $tmdb);
            
            if ( $mediaRow != '' ) {
                $genre_slider = 'genre-slider-'.$sliderNumber;

                echo '<div class="genre-slider '.$genre_slider.'">';
                    echo '<div class="col12 marg-top-l">';
                        echo '<div class="column">';
                            echo '<h3>'.$genre['genre_name'].'</h3>';
                        echo '</div>';

                        echo '<div class="column">'; 
                            echo '<div class="swiper card-slider">';
                                echo '<div class="swiper-wrapper">';
                                    echo $mediaRow;
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
                if ( $_SESSION['role'] === 'superadmin' || $_SESSION['role'] === 'admin'  ) {
                    echo '<p>'.lang_snippet('admin_setup_here').'</p>';
                } else {
                    echo '<p>'.lang_snippet('pls_wait_for_admin_setup').'</p>';
                }
            echo '</div>';
        echo '</div>';
    }

    echo '<div class="marg-bottom-l"></div>';

    $conn->close();
}

function goTrhoughMedia($db_genre, $conn) {
    $genreID = intval($db_genre);
    
    $query = "SELECT *
    FROM media INNER JOIN media_genre ON media.tmdbID = media_genre.media_id
    WHERE genre_id = $genreID
    ORDER BY RAND() LIMIT 20";
    $results = $conn->query($query);
    
    $mediaRow = '';

    if ($results->num_rows > 0) {
        // Es gibt mindestens einen Film des Genres
        while ( $media = $results->fetch_assoc() ) {
            $mediaRow = $mediaRow . media_card($media,'swiper-slide');           
        }
    } else {
        $mediaRow = '';
    }

    return $mediaRow;
}
?>