<?php
    //include(ROOT_PATH.'/config.php');
    $conn = dbConnect();
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');
    
    if ($cnf['apikey'] !== '') {
        $tmdb = new TMDB($cnf);
        $search = $_POST['show'];

        $shows = $tmdb->searchTVShow($search);
        
        if ($shows) {
            foreach ($shows as $show) {
                
                $id = $show->getID();
                $title = $show->getName();
                $poster = $show->getPoster();
                $dbShow = mediaInLocalDB($id);

                if( $dbShow !== true ) {
                    echo '<a href="#show-'.$id.'" class="display-flex flex-row marg-no" data-fancybox data-src="#show-'.$id.'">';
                        echo '<figure class="poster" style="width:20%;max-width:100px;">';
                            echo '<img src="'.loadImg('original', $poster).'" loading="lazy">';
                        echo '</figure>';
                        echo '<span class="pad-xs" style="width:80%;">'.$title.'</span>';
                    echo '</a>';

                    // Add Movie popup verification
                    echo '<div id="show-'.$id.'" style="display:none;">';
                        echo '<p>Möchtest du "'.$title.'" hinzufügen?</p>';
                        echo '<form name="show-'.$id.'-form" id="show-'.$id.'-form" method="post" action="/admin/shows">';
                            echo '<input type="text" name="id" value="'.$id.'" required style="display:none;">';
                            echo '<p class="text-right">';
                                echo '<button type="submit" class="btn btn-success" id="add-show" name="add-show">Hinzufügen</button>';
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