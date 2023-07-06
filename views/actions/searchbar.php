<?php
    //include(ROOT_PATH.'/config.php');
    $conn = dbConnect();
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');
    
    if ( isset($_POST['movie']) ) {
        
        $search = $_POST['movie'];

        $movies = selectMovieByTitle($search);
        
        if ($movies) {
            foreach ( $movies as $movie ) {    
                $movieID = $movie['movie_tmdbID'];
                $title = $movie['movie_title'];
                $movieOverview = $movie['movie_overview'];
                $movieRating = $movie['movie_rating'];
                $movieRuntime = $movie['movie_runtime'];
                $movieRelease = new DateTime($movie['movie_release']);
                $releaseYear = $movieRelease->format('Y');
                $moviePoster = $movie['movie_poster'];
                $backdrop = $movie['movie_thumbnail'];
                $genres = json_decode($movie['movie_genres']);
                $genreHTML = '';
                foreach ( $genres as $genre ) {
                    $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
                }
    
                echo '<a href="#movie-'.$movieID.'" class="display-flex flex-row marg-no" data-modal data-src="#content-'.$movieID.'">';
                    echo '<figure class="poster" style="width:20%;max-width:100px;">';
                        echo '<img src="/views/build/css/images/img_preview.webp" data-src="'.loadImg('w400', $moviePoster).'" class="lazy-load">';
                    echo '</figure>';
                    echo '<span class="pad-xs" style="width:80%;">'.$title.'</span>';
                echo '</a>';

                echo '<div class="info-popup" id="content-'.$movieID.'" style="display:none;">';
                    echo '<div class="col12 marg-bottom-xs mobile-only">';
                        echo '<figure class="widescreen">';
                            echo '<img src="/views/build/css/images/img_preview.webp" data-src="'.loadImg('w500', $backdrop).'" class="lazy-load">';
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
                                echo '<img src="/views/build/css/images/img_preview.webp" data-src="'.loadImg('w400', $moviePoster).'" alt="" class="lazy-load">';
                            echo '</figure>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            }
        } else if ( $_POST['movie'] !== '' ) {
            echo '<div class="col12 column pad-top-xs pad-bottom-xs">';
                echo '<p class="small marg-bottom-no">'.lang_snippet('no_movies_found').'</p>';
            echo '</div>';
        }
    }
?>