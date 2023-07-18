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
                    
                    // Add show from collection
                    if (isset($_POST['delete-show'])) {
                        //deleteShow($_POST['id']);
                    }    

                    callout();

                    echo '<div class="col7 marg-right-col1">';
                        echo '<form method="post" action="/admin/show/?id='.$id.'">';
                            echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                            echo '<button type="submit" class="btn btn-small btn-alert" name="delete-show">Löschen</button>';
                        echo '</form>';
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
                                echo '<button class="btn btn-small btn-success" id="trailerSubmit" type="submit">'.lang_snippet('save').'</button>';
                            echo '</form>';
                        echo '</div>';


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
                                    $episodesRow .= '
                                    <div class="col2">
                                    <figure class="widescreen">
                                    <img src="'.loadImg('original', $episode['backdrop']).'">
                                    <span>'.$episode['title'].'</span></div>';
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

                            echo '<div class="col12">';
                                echo '<ul class="tabs" data-tabs id="season-tabs">';
                                    echo $seasonTabList.$extras;
                                echo '</ul>';
                                echo '<div class="tabs-content" data-tabs-content="season-tabs">';
                                    echo $seasonContentList.$extrasContent;
                                echo '</div>';
                            echo '</div>';
                        }
                    echo '</div>';

                ?>
                <div class="col4">
                    <?php
                        if ( !isHighlight($id) ) {
                            echo '<div class="col12">';
                                echo '<form method="post" action="/admin/show/?id='.$_GET['id'].'">';
                                    echo '<input type="text" name="highlight" id="highlight" value="'.$_GET['id'].'" style="display:none;">';
                                    echo '<button class="btn btn-small" id="addHighlight" name="addHighlight" type="submit">'.lang_snippet('add_highlight').'</button>';
                                echo '</form>';
                            echo '</div>';
                        }
                    ?>

                    <div class="col12 marg-bottom-s">
                        <a href="#show-poster" data-fancybox data-src="#show-poster">
                            <figure class="poster">
                                <img src="<?php echo loadImg('original', $poster); ?>" loading="lazy">
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
                                                        echo '<img src="'.loadImg('original', $showPoster).'" loading="lazy">';
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

                    <div class="col12">
                        <a href="#show-backdrop" data-fancybox data-src="#show-backdrop">
                            <figure class="original">
                                <img src="<?php echo loadImg('original', $backdrop);?>" loading="lazy">
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
                                                    echo '<img src="'.loadImg('original', $showBackdrop).'" loading="lazy">';
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
        </div>
    </div>
</div>

<?php include('views/footer.php'); ?>
