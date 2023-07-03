<?php
include(ROOT_PATH.'/views/header.php');
$conn = dbConnect();
$tmdb = setupTMDB();
$cnf = tmdbConfig();

$movie = selectMovieByID($_GET['id']);
if ( $movie == 0 ) {
    page_redirect("/admin/movies");
} else {
    $id = $movie['id'];            
    $title = $movie['title'];
    $backdrop = $movie['backdrop'];
    $poster = $movie['poster'];       
    $tagline = $movie['tagline'];
    $genres = $movie['genres'];
    $currentMovieCollection = $movie['collection'];
    $filepath = $movie['file_path'];
}
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2 marg-right-col4">
            <div class="col12">
                <?php
                    // Update/select local path to movie file
                    if(isset($_POST['moviePath'])) {
                        updateMovieFilePath($_POST['moviePath'], $_POST['id']);
                    }

                    // Change movie poster
                    if(isset($_POST['change-poster'])) {
                        updateMoviePoster($_POST['id'], $_POST['poster']);
                    }

                    // Change movie thumbnail
                    if(isset($_POST['change-backdrop'])) {
                        updateMovieBackdrop($_POST['id'], $_POST['backdrop']);
                    }

                    // Add Movie from collection
                    if (isset($_POST['add-movie'])) {
                        insertMovie($_POST['id']);
                    }
                    
                    // Add Movie from collection
                    if (isset($_POST['delete-movie'])) {
                        deleteMovie($_POST['id']);
                    }    

                    callout();

                    echo '<div class="col7 marg-right-col1">';
                        echo '<form method="post" action="/admin/movie/?id='.$id.'">';
                            echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                            echo '<button type="submit" name="delete-movie">Löschen</button>';
                        echo '</form>';
                        echo '<div class="col12"><h1>'.$title.'</h1></div>';
                        if(($tagline > 1)) {
                            echo '<div class="col12"><p>'.$tagline.'</p></div>';
                        }
                        echo '<div class="col12"><p>'.$movie['overview'].'</p></div>';
                        echo '<div class="col3"><p><strong>'.lang_snippet('rating').':</strong><br>'.$movie['voteAverage'].'/10</p></div>';
                        echo '<div class="col5"><p><strong>'.lang_snippet('release_date').':</strong><br>'.outputDate($movie['release']).'</p></div>';
                        echo '<div class="col4"><p><strong>'.lang_snippet('runtime').':</strong><br>'.runtimeToString($movie['runtime']).'</p></div>';
                        echo '<div class="col12"><p><span><strong>'.lang_snippet('genres').':</strong></span><br>';                    
                        
                        foreach ($genres as $genre) {
                            echo '<span class="tag">'.$genre['name'].'</span>';
                        }
                        echo '</p></div>';
                    echo '</div>';

                ?>
                <div class="col4">
                    <div class="col12">
                        <?php
                            echo '<a href="#file-list-popup" class="btn" data-fancybox data-src="#file-list-popup">'.lang_snippet('select_movie_file').'</a>';
                                
                            echo '<div id="file-list-popup" style="display:none;">';
                                echo '<div id="file-tree"></div>';
                                echo '<form method="post" action="/admin/movie/?id='.$_GET['id'].'">';
                                    echo '<input type="text" name="moviePath" id="inputMoviePath" value="" style="display:none;">';
                                    echo '<input type="text" name="id" value="'.$_GET['id'].'" style="display:none;">';
                                    echo '<button class="btn marg-top-m marg-bottom-no" id="inputMovieSubmit" type="submit" style="display:none;">'.lang_snippet('save').'</button>';
                                echo '</form>';
                            echo '</div>';

                            if ( $filepath != '') {
                                videoPlayer($_GET['id']);
                                echo '<div class="marg-bottom-s"></div>';
                            }
                        ?>
                    </div>

                    <div class="col12 marg-bottom-s">
                        <a href="#movie-poster" data-fancybox data-src="#movie-poster">
                            <figure class="poster">
                                <img src="<?php echo $tmdb->getImageURL().$poster; ?>" loading="lazy">
                            </figure>
                        </a>
                           
                        <div id="movie-poster" style="display:none;">
                            <p><?php echo lang_snippet('select_new_poster');?>:</p>
                            <form method="post" action="/admin/movie/?id=<?php echo $id; ?>">
                                <div class="row">
                                    <?php
                                        $newTMDB = new TMDB($cnf);
                                        $newTMDB->setLang();
                                        $movie = $newTMDB->getMovie($id);
                                        $moviePosters = $movie->getPosters();
                                        foreach ($moviePosters as $moviePoster) {
                                            $i = 1;
                                            echo '<div class="col3 column">';
                                                echo '<div class="poster-select">';
                                                    echo '<input type="radio" id="poster-'.$i.'" name="poster" value="'.$moviePoster.'">';
                                                    echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                                    echo '<figure class="poster">';
                                                        echo '<img src="'.$tmdb->getImageURL().$moviePoster.'" loading="lazy">';
                                                    echo '</figure>';
                                                echo '</div>';
                                            echo '</div>';
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
                        <a href="#movie-backdrop" data-fancybox data-src="#movie-backdrop">
                            <figure class="original">
                                <img src="<?php echo $tmdb->getImageURL().$backdrop;?>" loading="lazy">
                            </figure>
                        </a>

                        <div id="movie-backdrop" style="display:none;">
                            <p><?php echo lang_snippet('select_new_thumbnail');?>:</p>
                            <form method="post" action="/admin/movie/?id=<?php echo $id; ?>">
                            <div class="row">
                                <?php
                                    $newTMDB = new TMDB($cnf);
                                    $newTMDB->setLang('');
                                    $movie = $newTMDB->getMovie($id);
                                    $movieBackdrops = $movie->getBackdrops();
                                    foreach ($movieBackdrops as $movieBackdrop) {
                                        $i = 1;
                                        echo '<div class="col3 column">';
                                            echo '<div class="poster-select">';
                                                echo '<input type="radio" id="backdrop-'.$i.'" name="backdrop" value="'.$movieBackdrop.'">';
                                                echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                                echo '<figure class="original">';
                                                    echo '<img src="'.$tmdb->getImageURL().$movieBackdrop.'" loading="lazy">';
                                                echo '</figure>';
                                            echo '</div>';
                                        echo '</div>';
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
            
            <?php
            if ( !($currentMovieCollection == 0) ) {
                echo '<div class="col12">';
                    echo '<div class="row">';
                    $collection = $tmdb->getCollection($currentMovieCollection);
                    $movies = $collection->getMovies();

                    foreach ($movies as $movie) {
                        $movieID = $movie->getID();

                        if ( movieIsInCollection($movieID) !== true ) {
                            echo '<div class="col3 column">';
                                echo '<a href="#add-movie-'.$movieID.'" class="media-card" data-fancybox data-src="#add-movie-'.$movieID.'">';
                                    echo '<figure class="poster">';
                                        echo '<img src="'.$tmdb->getImageURL().$movie->getPoster().'" alt="" loading="lazy">';
                                    echo '</figure>';
                                    echo '<span class="title">'.$movie->getTitle().'</span>';
                                echo '</a>';

                                echo '<div id="add-movie-'.$movieID.'" style="display:none;">';
                                    echo '<p>'.lang_snippet('add_movie').'</p>';
                                    echo '<form method="post" action="/admin/movie/?id='.$id.'">';
                                    echo '<input type="number" name="id" value="'.$movieID.'" style="display:none;">';
                                        echo '<p class="text-right">';
                                            echo '<button type="submit" class="btn btn-success" name="add-movie">'.lang_snippet('add').'</button>';
                                        echo '</p>';
                                    echo '</form>';
                                echo '</div>';
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                echo '</div>';
            }     
            ?>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>
