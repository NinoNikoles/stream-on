<?php
//-- Movie Card --
function movie_card($movie, $extraClasses = '') {
    $conn = dbConnect();
 
    $movieID = $movie['tmdbID'];
    $title = $movie['title'];
    $overview = $movie['overview'];
    $rating = $movie['rating'];
    $runtime = $movie['runtime'];
    $movieRelease = new DateTime($movie['release']);
    $releaseYear = $movieRelease->format('Y');
    $poster = $movie['poster'];
    $backdrop = $movie['backdrop'];
    $genres = json_decode($movie['genres']);
    $genreHTML = '';
    foreach ( $genres as $genre ) {
        $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
    }

    $userID = intval($_SESSION['userID']);

    $watchingSQL = "SELECT watched_seconds, total_length FROM movie_watched WHERE user_id = $userID and movie_id = $movieID and watched_seconds > 0";
    $watchInfos = $conn->query($watchingSQL);
    if ( $watchInfos->num_rows > 0 ) {
        while ( $watchInfo = $watchInfos->fetch_assoc() ) {
            $watchedInPercent = getWatchedTime($watchInfo['watched_seconds'], $watchInfo['total_length']);
        }
        $timebar = '<div class="watched-bar"><progress max="100" value="'.$watchedInPercent.'"></progress></div>';
    } else {
        $timebar = '';
    }

    $watchListCheckSQL = "SELECT id FROM watchlist WHERE user_id=$userID and movie_id=$movieID";

    if ( $conn->query($watchListCheckSQL)->num_rows > 0 ) {
        $listButtons = '
        <a href="#" class="btn btn-small btn-white icon-left icon-add mylist-btn add-to-list hidden loading" data-movie-id="'.$movieID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-small btn-white icon-left icon-remove mylist-btn remove-from-list loading" data-movie-id="'.$movieID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    } else {
        $listButtons = '
        <a href="#" class="btn btn-small btn-white icon-left icon-add mylist-btn add-to-list loading" data-movie-id="'.$movieID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-small btn-white icon-left icon-remove mylist-btn remove-from-list hidden loading" data-movie-id="'.$movieID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    }

    if ( $_SESSION['role'] === "1" ) {
        $editBtn = '<a href="/admin/movie/?id='.$movieID.'" title="'.lang_snippet('edit').'" class="edit-trigger"></a>';
    }

    $card = '
        <div class="'.$extraClasses.'">
            <div class="media-card">
                <div class="media-card-wrapper">
                    <figure class="widescreen desktop-only">
                        <img src="'.loadImg('original', $backdrop).'" loading="lazy" importance="low">
                    </figure>
                    <figure class="poster mobile-only">
                        <img src="'.loadImg('original', $poster).'" loading="lazy" importance="low">
                    </figure>
                    <div class="link-wrapper">
                        <a href="/watch/?id='.$movieID.'" title="'.$title.'" class="play-trigger"></a>
                        <a href="#content-'.$movieID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                        '.$editBtn.'
                    </div>
                </div>
                '.$timebar.'

                <div class="info-popup" id="content-'.$movieID.'" style="display:none;">
                    <div class="col12 marg-bottom-xs mobile-only">
                        <figure class="widescreen">
                            <img src="'.loadImg('original', $backdrop).'" loading="lazy" importance="low">
                        </figure>
                    </div>
                    <div class="innerWrap">
                        <div class="col7 marg-right-col1">
                            <p class="h2">'.$title.'</p>
                            <p class="small tag-list marg-bottom-base">
                                <span class="tag">'.$releaseYear.'</span>
                                <span class="tag">'.$rating.'/10 ★</span>
                                <span class="tag">'.runtimeToString($runtime).'</span>
                            </p>
                            <a href="/watch/?id='.$movieID.'" class="btn btn-small btn-white icon-left icon-play marg-right-xs">Jetzt schauen</a>
                            '.$listButtons.'
                            <p class="small">'.$overview.'</p>
                            <p class="small tag-list">'.$genreHTML.'</p>
                        </div>
                        <div class="col4 desktop-only">
                            <figure class="poster">
                                <img src="'.loadImg('original', $poster).'" alt="" loading="lazy" importance="low">
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    $conn->close();
    return $card;
}

function currentWatchlist() {
    $conn = dbConnect();
    $userID = $_SESSION['userID'];

    $query = "SELECT * FROM movies INNER JOIN movie_watched ON movies.movie_tmdbID = movie_watched.movie_id WHERE movie_watched.user_id = $userID and movie_watched.watched_seconds > 0";
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
                            $currMovie = moviesDataconverter($movie);
                            echo movie_card($currMovie, 'swiper-slide');
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

    $query = "SELECT * FROM movies INNER JOIN watchlist ON movies.movie_tmdbID = watchlist.movie_id WHERE watchlist.user_id = $userID ORDER BY movie_title ASC";
    $results = $conn->query($query);
    
    if ( $results->num_rows > 0 ) {    
        while ($movies = $results->fetch_assoc()) {
            $currMovie = moviesDataconverter($movies);
            echo movie_card($currMovie, 'col-6 col-4-xsmall col-2-medium grid-padding');
        }
    }

    $conn->close();
}

function genreSlider() {
    $conn = dbConnect();
    $tmdb = setupTMDB(); // 28, 12, 16, 80, 18, 878, 53

    $sql = "SELECT DISTINCT g.genre_id, g.genre_name FROM genres g WHERE g.genre_id IN (SELECT DISTINCT mg.genre_id FROM movie_genre mg)";
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

    echo '<div class="marg-bottom-l"></div>';

    $conn->close();
}

function goTrhoughMovies($db_genre, $conn) {
    $genreID = intval($db_genre);
    
    $query = "SELECT movie_tmdbID, movie_title, movie_tagline, movie_overview, movie_poster, movie_thumbnail, movie_rating, movie_release, movie_runtime, movie_genres FROM movies INNER JOIN movie_genre ON movies.movie_tmdbID = movie_genre.movie_id WHERE movie_genre.genre_id = $genreID ORDER BY RAND() LIMIT 20";
    $results = $conn->query($query);
    
    $movieRow = '';

    if ($results->num_rows > 0) {
        // Es gibt mindestens einen Film des Genres
        while ( $movie = $results->fetch_assoc() ) {
            $currentMovie = moviesDataconverter($movie);
            $movieRow = $movieRow . movie_card($currentMovie, 'swiper-slide');
        }
    } else {
        $movieRow = '';
    }

    return $movieRow;
}
?>