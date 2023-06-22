<?php
    //include(ROOT_PATH.'/config.php');
    $conn = dbConnect();
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');
    
    $tmdb = new TMDB($cnf);
    
    if ( isset($_POST['movie']) ) {
        
        $search = $_POST['movie'];

        $movies = selectMovieByTitle($search);
        
        if ($movies) {
            foreach ( $movies as $movie ) {    
                $movieID = $movie['id'];
                $title = $movie['title'];
                $movieOverview = $movie['overview'];
                $movieRating = $movie['voteAverage'];
                $movieRuntime = $movie['runtime'];
                $movieRelease = new DateTime($movie['release']);
                $releaseYear = $movieRelease->format('Y');
                $poster = $movie['poster'];
                $backdrop = $movie['backdrop'];
                $genres = $movie['genres'];
                $genreHTML = '';
                foreach ( $genres as $genre ) {
                    $genreHTML = $genreHTML . '<span class="tag">'.$genre['name'].'</span>';
                }
    
                if( $dbMovie !== true ) {
                    echo '<a href="#movie-'.$movieID.'" class="display-flex flex-row marg-no" data-modal data-src="#content-'.$movieID.'">';
                        echo '<figure class="poster" style="width:20%;max-width:100px;">';
                            echo '<img src="'.$tmdb->getImageURL().$poster.'">';
                        echo '</figure>';
                        echo '<span class="pad-xs" style="width:80%;">'.$title.'</span>';
                    echo '</a>';

                    echo '<div class="info-popup" id="content-'.$movieID.'" style="display:none;">';
                        echo '<div class="col12 marg-bottom-xs mobile-only">';
                            echo '<figure class="widescreen">';
                                echo '<img src="'.$tmdb->getImageURL().$backdrop.'">';
                            echo '</figure>';
                        echo '</div>';
                        echo '<div class="innerWrap">';
                            echo '<div class="col7 marg-right-col1">';
                                echo '<p class="h2">'.$title.'</p>';
                                echo '<p class="small">';
                                    echo '<span class="tag">'.$releaseYear.'</span>';
                                    echo '<span class="tag">'.$movieRating.'/10</span>';
                                    echo '<span class="tag">'.runtimeToString($movieRuntime).'</span>';
                                echo '</p>';
                                echo '<a href="/watch/?id='.$movieID.'" class="btn btn-white icon-left icon-play">Jetzt schauen</a>';
                                echo '<p class="small">'.$movieOverview.'</p>';
                                echo '<p class="small">'.$genreHTML.'</p>';
                            echo '</div>';
                            echo '<div class="col4 desktop-only">';
                                echo '<figure class="poster">';
                                    echo '<img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">';
                                echo '</figure>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                } else {
                    echo '<div class="col12 column pad-top-xs pad-bottom-xs">';
                        echo '<p class="small marg-bottom-no">'.lang_snippet('no_movies_found').'</p>';
                    echo '</div>';
                }
            }
        } else if ( $_POST['movie'] !== '' ) {
            echo '<div class="col12 column pad-top-xs pad-bottom-xs">';
                echo '<p class="small marg-bottom-no">'.lang_snippet('no_movies_found').'</p>';
            echo '</div>';
        }
    }
?>