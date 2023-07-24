<?php
include(ROOT_PATH.'/views/header.php');
$conn = dbConnect();
$tmdb = setupTMDB();
$cnf = tmdbConfig();

$show = selectShowByID($_GET['id']);
if ( $show == 0 ) {
    page_redirect("/admin/shows");
} else {
    $id = $show['tmdbID'];            
    $title = $show['title'];
    $backdrop = $show['backdrop'];
    $poster = $show['poster'];
    $genres = $show['genres'];
    $tailer = $show['trailer'];
}
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2 marg-right-col4">
            <div class="col12">
                <?php

                    // Update trailer
                    if(isset($_POST['trailer'])) {
                        updateShowTrailer($_POST['id'], $_POST['trailer']);
                    }

                    // Add highlight
                    if(isset($_POST['highlight'])) {
                        addHighlight($_POST['highlight']);
                    }

                    // Change show poster
                    if(isset($_POST['change-poster'])) {
                        updateShowPoster($_POST['id'], $_POST['poster']);
                    }

                    // Change show thumbnail
                    if(isset($_POST['change-backdrop'])) {
                        updateShowBackdrop($_POST['id'], $_POST['backdrop']);
                    }
                    
                    // Delete show
                    if (isset($_POST['delete-show'])) {
                        deleteShow($_POST['id']);
                    }

                    // Update Show
                    if (isset($_POST['update-show'])) {
                        updateShow($_POST['id']);
                    }   

                    callout();
                    
                    // Main content
                    echo '<div class="col7 marg-right-col1">';

                        echo '<div class="col12"><h1>'.$title.'</h1></div>';
                        echo '<div class="col12"><p>'.$show['overview'].'</p></div>';
                        echo '<div class="col3"><p><strong>'.lang_snippet('rating').':</strong><br>'.$show['rating'].'/10</p></div>';
                        echo '<div class="col5"><p><strong>'.lang_snippet('release_date').':</strong><br>'.outputDate($show['release']).'</p></div>';
                        echo '<div class="col12"><p><span><strong>'.lang_snippet('genres').':</strong></span><br>';                
                        
                        foreach ($genres as $genre) {
                            echo '<span class="tag">'.$genre['name'].'</span>';
                        }
                        echo '</p></div>';
                        echo '<div class="col12">';
                            echo '<form method="post" action="/admin/show/?id='.$_GET['id'].'">';
                                echo '<label for="trailer"><strong>'.lang_snippet('trailer').':</strong> <input type="text" id="trailer" name="trailer" value="'.$tailer.'"></label>';
                                echo '<span class="smaller">'.lang_snippet('trailer_info').'</span>';
                                echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                echo '<button class="btn btn-small btn-success icon-left icon-save" id="trailerSubmit" type="submit">'.lang_snippet('save').'</button>';
                            echo '</form>';
                        echo '</div>';
                    echo '</div>';

                ?>
                <div class="col4">
                    <div class="row">
                        <?php
                            echo '<div class="column col6">';
                                // Delete Btn
                                echo '<form method="post" action="/admin/show/?id='.$id.'">';
                                    echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                    echo '<button type="submit" class="btn btn-small btn-alert icon-left icon-trash" name="delete-show">'.lang_snippet('delete').'</button>';
                                echo '</form>';
                            echo '</div>';
                            echo '<div class="column col6">';
                                // Update Btn
                                echo '<form method="post" action="/admin/show/?id='.$id.'">';
                                    echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                    echo '<button type="submit" class="btn btn-small btn-warning icon-left icon-update" id="update-show" name="update-show">'.lang_snippet('update').'</button>';
                                echo '</form>';
                            echo '</div>';
                        ?>

                        <!-- Highlight -->
                        <?php
                            if ( !isHighlight($id) ) {
                                echo '<div class="column col12">';
                                    echo '<form method="post" action="/admin/show/?id='.$_GET['id'].'">';
                                        echo '<input type="text" name="highlight" id="highlight" value="'.$_GET['id'].'" style="display:none;">';
                                        echo '<button class="btn btn-small btn-white icon-left icon-add" id="addHighlight" name="addHighlight" type="submit">'.lang_snippet('add_highlight').'</button>';
                                    echo '</form>';
                                echo '</div>';
                            }
                        ?>

                        <!-- Poster -->
                        <div class="column col-6 marg-bottom-s">
                            <a href="#show-poster" data-fancybox data-src="#show-poster">
                                <figure class="poster">
                                    <img data-img="<?php echo loadImg('original', $poster); ?>" loading="lazy">
                                </figure>
                            </a>
                            
                            <div id="show-poster" style="display:none;">
                                <p><?php echo lang_snippet('select_new_poster');?>:</p>
                                <form method="post" action="/admin/show/?id=<?php echo $id; ?>">
                                    <div class="row">
                                        <?php
                                            $newTMDB = new TMDB($cnf);
                                            $newTMDB->setLang();
                                            $show = $newTMDB->getTVShow($id);
                                            $showPosters = $show->getPosters();
                                            $i = 1;
                                            foreach ($showPosters as $showPoster) {
                                                
                                                echo '<div class="col3 column">';
                                                    echo '<div class="poster-select">';
                                                        echo '<input type="radio" id="poster-'.$i.'" name="poster" value="'.$showPoster.'">';
                                                        echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                                        echo '<figure class="poster">';
                                                            echo '<img data-img="'.loadImg('original', $showPoster).'" loading="lazy">';
                                                        echo '</figure>';
                                                    echo '</div>';
                                                echo '</div>';
                                                $i++;
                                            }
                                        ?>
                                        </div>
                                    
                                    <p class="text-right">
                                        <button type="submit" class="btn btn-success" name="change-poster"><?php echo lang_snippet('save'); ?></button>
                                    </p>
                                </form>
                            </div>
                        </div>

                        <!-- Backdrop -->
                        <div class="column col-6">
                            <a href="#show-backdrop" data-fancybox data-src="#show-backdrop">
                                <figure class="original">
                                    <img data-img="<?php echo loadImg('original', $backdrop);?>" loading="lazy">
                                </figure>
                            </a>

                            <div id="show-backdrop" style="display:none;">
                                <p><?php echo lang_snippet('select_new_thumbnail');?>:</p>
                                <form method="post" action="/admin/show/?id=<?php echo $id; ?>">
                                <div class="row">
                                    <?php
                                        $newTMDB = new TMDB($cnf);
                                        $newTMDB->setLang('');
                                        $show = $newTMDB->getTVShow($id);
                                        $showBackdrops = $show->getBackdrops();
                                        $i = 1;

                                        foreach ($showBackdrops as $showBackdrop) {
                                            echo '<div class="col3 column">';
                                                echo '<div class="poster-select">';
                                                    echo '<input type="radio" id="backdrop-'.$i.'" name="backdrop" value="'.$showBackdrop.'">';
                                                    echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                                    echo '<figure class="original">';
                                                        echo '<img data-img="'.loadImg('original', $showBackdrop).'" loading="lazy">';
                                                    echo '</figure>';
                                                echo '</div>';
                                            echo '</div>';
                                            $i++;
                                        }
                                    ?>
                                    </div>
                                    <p class="text-right">
                                        <button type="submit" class="btn btn-success" name="change-backdrop"><?php echo lang_snippet('save'); ?></button>
                                    </p>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col12">
                <?php
                    $querySeasons = "SELECT tmdbID, title, season_number, episodes_count FROM seasons WHERE show_tmdbID=$id";
                    $seasonResults = $conn->query($querySeasons);
                    $seasonTabList = '';
                    $seasonContentList = '';
                    $extras = '';
                    $extrasContent = '';

                    if ( $seasonResults->num_rows > 0 ) {
                        while ( $seasonRow = $seasonResults->fetch_assoc() ) {
                            $seasonNumber = $seasonRow['season_number'];
                            $sql = "SELECT * FROM episodes WHERE show_id=$id AND season_number=$seasonNumber";
                            $episodeResult = $conn->query($sql);
                            
                            $episodesRow = '';

                            while ( $episode = $episodeResult->fetch_assoc() ) {
                                $disabled = 'disabled';

                                if ( $episode['file_path'] != "" ) {
                                    $disabled = '';
                                }

                                $episodesRow .= '
                                <div class="col4 '.$disabled.'">
                                    <figure class="widescreen">
                                        <img data-img="'.loadImg('original', $episode['backdrop']).'">
                                    </figure>
                                    <span class="small marg-top-xxs">'.$episode['title'].'</span>
                                    <a href="#file-list-popup-'.$episode['tmdbID'].'" class="btn btn-small btn-success" data-fancybox data-src="#file-list-popup-'.$episode['tmdbID'].'">'.lang_snippet('select_movie_file').'</a>
                                    <div id="file-list-popup-'.$episode['tmdbID'].'" style="display:none;">
                                        <div class="file-tree-episode" data-element-id="'.$episode['tmdbID'].'">
                                            <a href="#" class="btn marg-top-m marg-bottom-no episode-path-submit" data-submit="'.$episode['tmdbID'].'" data-path="" style="display:none;">'.lang_snippet('save').'</a>
                                        </div>
                                    </div>
                                </div>';
                            }

                            if ( $seasonNumber === '0' ) {                                  

                                // Tab - Extras
                                $extras = '<li class="tabs-title"><a href="#season-'.$seasonNumber.'">'.$seasonRow['title'].'</a></li>';
                                //Content - Extras
                                $extrasContent = '<div class="col12 tabs-panel" id="season-'.$seasonNumber.'">'.$episodesRow.'</div>';

                            } else if ( $seasonNumber === '1' ) {
                                // Tab - Season 1
                                $seasonTabList = $seasonTabList.'<li class="tabs-title" class="is-active"><a href="#season-'.$seasonNumber.'" aria-selected="true">'.$seasonRow['title'].'</a></li>';
                                // Content - Season 1
                                $seasonContentList = $seasonContentList.'<div class="col12 tabs-panel is-active" id="season-'.$seasonNumber.'">'.$episodesRow.'</div>';

                            } else {
                                // Tab - Rest of Seasons
                                $seasonTabList = $seasonTabList.'<li class="tabs-title"><a href="#season-'.$seasonNumber.'">'.$seasonRow['title'].'</a></li>';
                                // Content - Rest of Seasons
                                $seasonContentList = $seasonContentList.'<div class="col12 tabs-panel" id="season-'.$seasonNumber.'">'.$episodesRow.'</div>';
                            }
                        }

                        echo '<div class="col12 marg-top-s">';
                            echo '<ul class="tabs" data-tabs id="season-tabs">';
                                echo $seasonTabList.$extras;
                            echo '</ul>';
                            echo '<div class="tabs-content" data-tabs-content="season-tabs">';
                                echo $seasonContentList.$extrasContent;
                            echo '</div>';
                        echo '</div>';
                    }
                ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('views/footer.php'); ?>
