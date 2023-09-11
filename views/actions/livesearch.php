<?php
    $conn = dbConnect(); 
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');
    $result = "";
    
    if ($cnf['apikey'] !== '') {
        $tmdb = new TMDB($cnf);
        
        if ( isset($_POST['media']) && $_POST['media'] > 0 ) {
            $search = $_POST['media'];

            $media = selectAllMediaByTitle($search);

            if ($media) {
                foreach ( $media as $data ) {    
                    $result .= media_card($data, 'col-6 col-4-xsmall col-2-medium grid-padding');
                }

                echo $result;
            } else if ( $_POST['media'] !== '' ) {
                echo '<p>'.lang_snippet('no_content_found').'</p>';
            }
        }
    }
?>