<?php
    //include(ROOT_PATH.'/config.php');
    $conn = dbConnect();
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');
    
    if ($cnf['apikey'] !== '') {
        $tmdb = new TMDB($cnf);
        $search = $_POST['movie'];

        $movies = $tmdb->searchMovie($search);
        
        if ($movies) {
            foreach ($movies as $movie) {
                
                $id = $movie->getID();
                $title = $movie->getTitle();
                $poster = $movie->getPoster();
                $dbMovie = mediaInLocalDB($id);

                if( $dbMovie !== true ) {
                    echo '<a href="#movie-'.$id.'" class="display-flex flex-row marg-no" data-fancybox data-src="#movie-'.$id.'">';
                        echo '<figure class="poster" style="width:20%;max-width:100px;">';
                            echo '<img src="'.loadImg('original', $poster).'" loading="lazy">';
                        echo '</figure>';
                        echo '<span class="pad-xs" style="width:80%;">'.$title.'</span>';
                    echo '</a>';

                    // Add Movie popup verification
                    echo '<div id="movie-'.$id.'" style="display:none;">';
                        echo '<p>Möchtest du "'.$title.'" hinzufügen?</p>';
                        echo '<form name="movie-'.$id.'-form" id="movie-'.$id.'-form" method="post" action="/admin/movies">';
                            echo '<input type="text" name="id" value="'.$id.'" required style="display:none;">';
                            echo '<p class="text-right">';
                                echo '<button type="submit" class="btn btn-success" name="add-movie">Hinzufügen</button>';
                            echo '</p>';
                        echo '</form>';
                    echo '</div>';
                }
            }
        } else {
            echo '<p>'.lang_snippet('no_movies_found').'</p>';
        }
    } else {
        echo '<p>'.lang_snippet('no_movies_found').'</p>';
    }
?>