<?php
//-- Movie Card --
function movie_card($movie, $extraClasses = '') {
    $conn = dbConnect();
    $tmdb = setupTMDB();
 
    $movieID = $movie['movie_tmdbID'];
    $movieTitle = $movie['movie_title'];
    $movieOverview = $movie['movie_overview'];
    $movieRating = $movie['movie_rating'];
    $movieRuntime = $movie['movie_runtime'];
    $movieRelease = new DateTime($movie['movie_release']);
    $releaseYear = $movieRelease->format('Y');
    $moviePoster = $movie['movie_poster'];
    $movieBackdrop = $movie['movie_thumbnail'];
    /*$genres = json_decode($movie['movie_genres']);
    $genreHTML = '';
    foreach ( $genres as $genre ) {
        $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
    }*/

    $userID = intval($_SESSION['userID']);

    $watchListCheckSQL = "SELECT id FROM watchlist WHERE user_id=$userID and movie_id=$movieID";

    if ( $conn->query($watchListCheckSQL)->num_rows > 0 ) {
        $listButtons = '
        <a href="#" class="btn btn-white icon-left icon-add mylist-btn add-to-list hidden" data-movie-id="'.$movieID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-white icon-left icon-remove mylist-btn remove-from-list" data-movie-id="'.$movieID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    } else {
        $listButtons = '
        <a href="#" class="btn btn-white icon-left icon-add mylist-btn add-to-list" data-movie-id="'.$movieID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-white icon-left icon-remove mylist-btn remove-from-list hidden" data-movie-id="'.$movieID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    }

    $card = '
        <div class="'.$extraClasses.'">
            <div class="media-card widescreen-media-card desktop-only">
                <figure class="widescreen">
                    <img src="/views/build/css/images/img_preview.webp" data-src="'.$tmdb->getImageURL('w400').$movieBackdrop.'" class="lazy-load" importance="low">
                </figure>
                <div class="link-wrapper">
                    <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                    <a href="#content-'.$movieID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                </div>
            </div>

            <div class="media-card mobile-only">
                <figure class="poster">
                    <img src="/views/build/css/images/img_preview.webp" data-src="'.$tmdb->getImageURL('w500').$moviePoster.'" class="lazy-load" importance="low">
                </figure>
                <div class="link-wrapper">
                    <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                    <a href="#content-'.$movieID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                </div>
            </div>

            <div class="info-popup" id="content-'.$movieID.'" style="display:none;">
                <div class="col12 marg-bottom-xs mobile-only">
                    <figure class="widescreen">
                        <img src="/views/build/css/images/img_preview.webp" data-src="'.$tmdb->getImageURL('w400').$movieBackdrop.'" class="lazy-load" importance="low">
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
                        '.$listButtons.'
                        <p class="small">'.$movieOverview.'</p>
                    </div>
                    <div class="col4 desktop-only">
                        <figure class="poster">
                            <img src="/views/build/css/images/img_preview.webp" data-src="'.$tmdb->getImageURL('w500').$moviePoster.'" alt="" class="lazy-load" importance="low">
                        </figure>
                    </div>
                </div>
            </div>
        </div>';
    $conn->close();
    return $card;
}

function currentWatchlist() {
    $conn = dbConnect();
    $tmdb = setupTMDB();
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
                            $movieID = $movie['movie_tmdbID'];
                            $watched = intval($movie['watched']);

                            if ( $watched !== 1 ) {                                
                                $movieTitle = $movie['movie_title'];
                                $movieOverview = $movie['movie_overview'];
                                $movieRating = $movie['movie_rating'];
                                $movieRuntime = $movie['movie_runtime'];
                                $movieRelease = new DateTime($movie['movie_release']);
                                $releaseYear = $movieRelease->format('Y');
                                $moviePoster = $movie['movie_poster'];
                                $movieBackdrop = $movie['movie_thumbnail'];
                                /* $genres = json_decode($movie['movie_genres']);
                                $genreHTML = '';
                                foreach ( $genres as $genre ) {
                                    $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
                                }*/
                                $watchedTime = floatval($movie['watched_seconds']);
                                $totalDuration = floatval($movie['total_length']);
                                $watchedInPercent = ($watchedTime/$totalDuration)*100;

                                $watchListCheckSQL = "SELECT * FROM watchlist WHERE user_id=$userID and movie_id=$movieID";
                            
                                if ( $conn->query($watchListCheckSQL)->num_rows > 0 ) {
                                    $listButtons = '
                                    <a href="#" class="btn btn-white icon-left icon-add mylist-btn add-to-list hidden" data-movie-id="'.$movieID.'" data-type="add">'.lang_snippet('my_list').'</a>
                                    <a href="#" class="btn btn-white icon-left icon-remove mylist-btn remove-from-list" data-movie-id="'.$movieID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
                                } else {
                                    $listButtons = '
                                    <a href="#" class="btn btn-white icon-left icon-add mylist-btn add-to-list" data-movie-id="'.$movieID.'" data-type="add">'.lang_snippet('my_list').'</a>
                                    <a href="#" class="btn btn-white icon-left icon-remove mylist-btn remove-from-list hidden" data-movie-id="'.$movieID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
                                }
    
                                echo    '<div class="swiper-slide">
                                            <div class="desktop-only">
                                                <div class="media-card widescreen-media-card">
                                                    <figure class="widescreen">
                                                        <img src="/views/build/css/images/img_preview.webp" data-src="'.$tmdb->getImageURL('w400').$movieBackdrop.'" alt="" class="lazy-load" importance="low">
                                                    </figure>
                                                    <div class="link-wrapper">
                                                        <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                                                        <a href="#content-'.$movieID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                                                    </div>
                                                </div>
                                                <div class="watched-bar">
                                                        <progress max="100" value="'.$watchedInPercent.'"></progress>
                                                    </div>
                                                </div>
                                            <div>

                                            <div class="mobile-only">
                                                <div class="media-card">
                                                    <figure class="poster">
                                                        <img src="/views/build/css/images/img_preview.webp" data-src="'.$tmdb->getImageURL('w500').$moviePoster.'" alt="" class="lazy-load" importance="low">
                                                    </figure>
                                                    <div class="link-wrapper">
                                                        <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                                                        <a href="#content-'.$movieID.'" title="'.$movieTitle.'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                                                    </div>
                                                </div>
                                                <div class="watched-bar">
                                                    <progress max="100" value="'.$watchedInPercent.'"></progress>
                                                </div>
                                            </div>
            
                                            <div class="info-popup" id="content-'.$movieID.'" style="display:none;">
                                                <div class="col12 marg-bottom-xs mobile-only">
                                                    <figure class="widescreen">
                                                        <img src="/views/build/css/images/img_preview.webp" data-src="'.$tmdb->getImageURL().$movieBackdrop.'" class="lazy-load" importance="low">
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
                                                        '.$listButtons.'
                                                        <p class="small">'.$movieOverview.'</p>
                                                    </div>
                                                    <div class="col4 desktop-only">
                                                        <figure class="poster">
                                                            <img src="/views/build/css/images/img_preview.webp" data-src="'.$tmdb->getImageURL().$moviePoster.'" alt="" class="lazy-load" importance="low">
                                                        </figure>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                            } else {
                                $updateWatched = "UPDATE movie_watched SET watched='1' WHERE user_id='$userID' and movie_id='$movieID'";
                                $conn->query($updateWatched);
                            }
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
    $tmdb = setupTMDB();
    $userID = $_SESSION['userID'];

    $query = "SELECT * FROM movies INNER JOIN watchlist ON movies.movie_tmdbID = watchlist.movie_id WHERE watchlist.user_id = $userID ORDER BY movie_title ASC";
    $results = $conn->query($query);
    
    if ( $results->num_rows > 0 ) {               
        while ( $movie = $results->fetch_assoc() ) {
            echo movie_card($movie, 'col-6 col-3-medium grid-padding');
        }
    }

    $conn->close();
}

function genreSlider() {
    $conn = dbConnect();
    $tmdb = setupTMDB(); // 28, 12, 16, 80, 18, 878, 53

    $sql = "SELECT DISTINCT g.genre_id, g.genre_name FROM genres g WHERE g.genre_id IN (SELECT DISTINCT mg.genre_id FROM movie_genre mg)";//"SELECT genre_id, genre_name FROM genres ORDER BY genre_id ASC";
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
?>