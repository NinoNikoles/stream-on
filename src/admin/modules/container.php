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
    $genres = json_decode($movie['movie_genres']);
    $genreHTML = '';
    foreach ( $genres as $genre ) {
        $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
    }

    $userID = intval($_SESSION['userID']);

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

    $card = '
        <div class="'.$extraClasses.'">
            <div class="media-card widescreen-media-card desktop-only">
                <figure class="widescreen">
                    <img src="'.$tmdb->getImageURL('w400').$movieBackdrop.'" alt="" loading="lazy">
                </figure>
                <div class="link-wrapper">
                    <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                    <a href="#content-'.$movieID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                </div>
            </div>

            <div class="media-card mobile-only">
                <figure class="poster">
                    <img src="'.$tmdb->getImageURL('w500').$moviePoster.'" alt="" loading="lazy">
                </figure>
                <div class="link-wrapper">
                    <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                    <a href="#content-'.$movieID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                </div>
            </div>

            <div class="info-popup" id="content-'.$movieID.'" style="display:none;">
                <div class="col12 marg-bottom-xs mobile-only">
                    <figure class="widescreen">
                        <img src="'.$tmdb->getImageURL().$movieBackdrop.'" loading="lazy">
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
                        <p class="small">'.$genreHTML.'</p>
                    </div>
                    <div class="col4 desktop-only">
                        <figure class="poster">
                            <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="" loading="lazy">
                        </figure>
                    </div>
                </div>
            </div>
        </div>';
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
                                $genres = json_decode($movie['movie_genres']);
                                $genreHTML = '';
                                foreach ( $genres as $genre ) {
                                    $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
                                }
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
                                                        <img src="'.$tmdb->getImageURL('w400').$movieBackdrop.'" alt="" loading="lazy">
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
                                                        <img src="'.$tmdb->getImageURL('w500').$moviePoster.'" alt="" loading="lazy">
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
                                                        <img src="'.$tmdb->getImageURL().$movieBackdrop.'" loading="lazy">
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
                                                        <p class="small">'.$genreHTML.'</p>
                                                    </div>
                                                    <div class="col4 desktop-only">
                                                        <figure class="poster">
                                                            <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="" loading="lazy">
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
}
?>