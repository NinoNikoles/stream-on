<?php
    $conn = dbConnect(); 
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');
    
    if ($cnf['apikey'] !== '') {
        $tmdb = new TMDB($cnf);
        
        if ( isset($_POST['movie']) ) {
            
            $search = $_POST['movie'];

            $movies = selectMovieByTitle($search);
            
            if ($movies) {
                foreach ( $movies as $movie ) {    
                    echo media_card($movie, 'col-6 col-4-xsmall col-2-medium grid-padding');
                }
            } else if ( $_POST['movie'] !== '' ) {
                echo '<p>'.lang_snippet('no_movies_found').'</p>';
            }
        }
    }
?>