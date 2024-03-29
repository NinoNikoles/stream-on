<?php
$pageTitle = pageTitle(getMediaTitle($_GET['id']));

include(ROOT_PATH.'/views/header.php');
$conn = dbConnect();
$tmdb = setupTMDB();
$cnf = tmdbConfig();

$movie = selectMovieByID($_GET['id']);
if ( $movie == 0 ) {
    page_redirect("/admin/movies");
} else {
    $id = $movie['tmdbID'];            
    $title = $movie['title'];
    $backdrop = $movie['backdrop'];
    $poster = $movie['poster'];       
    $tagline = $movie['tagline'];
    $genres = $movie['genres'];
    $currentMovieCollection = $movie['collection'];
    $filepath = $movie['file_path'];
    $tailer = $movie['trailer'];
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

                    // Update trailer
                    if(isset($_POST['trailer'])) {
                        updateMovieTrailer($_POST['id'], $_POST['trailer']);
                    }

                    // Add highlight
                    if(isset($_POST['highlight'])) {
                        addHighlight($_POST['highlight']);
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

                    // Main Content
                    echo '<div class="col7 marg-right-col1">';

                        echo '<div class="col12"><h1>'.$title.'</h1></div>';
                        if(($tagline > 1)) {
                            echo '<div class="col12"><p>'.$tagline.'</p></div>';
                        }
                        echo '<div class="col12"><p>'.$movie['overview'].'</p></div>';
                        echo '<div class="col3"><p><strong>'.lang_snippet('rating').':</strong><br>'.$movie['rating'].'/10</p></div>';
                        echo '<div class="col5"><p><strong>'.lang_snippet('release_date').':</strong><br>'.outputDate($movie['release']).'</p></div>';
                        echo '<div class="col4"><p><strong>'.lang_snippet('runtime').':</strong><br>'.runtimeToString($movie['runtime']).'</p></div>';
                        echo '<div class="col12"><p><span><strong>'.lang_snippet('genres').':</strong></span><br>';                
                        
                        foreach ($genres as $genre) {
                            echo '<span class="tag">'.$genre['name'].'</span>';
                        }
                        echo '</p></div>';
                        echo '<div class="col12">';
                            echo '<form method="post" action="/admin/movie/?id='.$_GET['id'].'">';
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
                        <!-- Delete -->
                        <?php
                            echo '<div class=" column col12">';
                                echo '<form method="post" action="/admin/movie/?id='.$id.'">';
                                    echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                    echo '<button type="submit" class="btn btn-small btn-alert icon-left icon-trash" id="delete-movie" name="delete-movie">'.lang_snippet('delete').'</button>';
                                echo '</form>';
                            echo '</div>';
                        ?>

                        <!-- Highlight -->
                        <?php
                            if ( !isHighlight($id) ) {
                                echo '<div class=" column col12">';
                                    echo '<form method="post" action="/admin/movie/?id='.$_GET['id'].'">';
                                        echo '<input type="text" name="highlight" id="highlight" value="'.$_GET['id'].'" style="display:none;">';
                                        echo '<button class="btn btn-white btn-small icon-left icon-add" id="addHighlight" name="addHighlight" type="submit">'.lang_snippet('add_highlight').'</button>';
                                    echo '</form>';
                                echo '</div>';
                            }
                        ?>

                        <!-- File Select -->
                        <div class="column col12">
                            <?php
                                echo '<a href="#file-list-popup" class="btn btn-small btn-warning icon-left icon-media" data-fancybox data-src="#file-list-popup">'.lang_snippet('select_movie_file').'</a>';
                                    
                                echo '<div id="file-list-popup" style="display:none;">';
                                    echo '<input type="text" id="jstree-search" value="" placeholder="Search" class="marg-bottom-s">';
                                    echo '<div id="file-tree"></div>';
                                    echo '<form method="post" action="/admin/movie/?id='.$_GET['id'].'">';
                                        echo '<input type="text" name="moviePath" id="inputMoviePath" value="" style="display:none;">';
                                        echo '<input type="text" name="id" id="jstreeID" value="'.$_GET['id'].'" style="display:none;">';
                                        echo '<button class="btn marg-top-m marg-bottom-no" id="inputMovieSubmit" type="submit" style="display:none;">'.lang_snippet('save').'</button>';
                                    echo '</form>';
                                echo '</div>';

                                if ( $filepath != '') {
                                    movieVideoPlayer($_GET['id']);
                                    echo '<div class="marg-bottom-s"></div>';
                                }
                            ?>
                        </div>

                        <!-- Poster -->
                        <div class="column col-6 marg-bottom-s">
                            <a href="#movie-poster" data-fancybox data-src="#movie-poster">
                                <figure class="poster">
                                    <img data-img="<?php echo loadImg('original', $poster); ?>" loading="lazy" alt="">
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
                                            $i = 1;
                                            foreach ($moviePosters as $moviePoster) {
                                                
                                                echo '<div class="col-6 col-3-medium column marg-bottom-base">';
                                                    echo '<div class="poster-select">';
                                                        echo '<input type="radio" id="poster-'.$i.'" name="poster" value="'.$moviePoster.'">';
                                                        echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                                        echo '<figure class="poster">';
                                                            echo '<img data-img="'.loadImg('original', $moviePoster).'" loading="lazy" alt="">';
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
                            <a href="#movie-backdrop" data-fancybox data-src="#movie-backdrop">
                                <figure class="original">
                                    <img data-img="<?php echo loadImg('original', $backdrop);?>" loading="lazy" alt="">
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
                                        $i = 1;

                                        foreach ($movieBackdrops as $movieBackdrop) {
                                            echo '<div class="col-6 col-3-medium column marg-bottom-base">';
                                                echo '<div class="poster-select">';
                                                    echo '<input type="radio" id="backdrop-'.$i.'" name="backdrop" value="'.$movieBackdrop.'">';
                                                    echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                                    echo '<figure class="original">';
                                                        echo '<img data-img="'.loadImg('original', $movieBackdrop).'" loading="lazy" alt="">';
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
            
            <?php
            if ( !($currentMovieCollection == 0) ) {
                echo '<div class="col12 marg-top-l">';
                    echo '<h2>'.lang_snippet('similar').':</h2>';

                    echo '<div class="row">';
                    $collection = $tmdb->getCollection($currentMovieCollection);
                    $movies = $collection->getMovies();

                    foreach ($movies as $movie) {
                        $movieID = $movie->getID();

                        if ( movieIsInCollection($movieID) !== true ) {
                            echo '<div class="col-6 col-3-medium column">';
                                echo '<a href="#add-movie-'.$movieID.'" class="media-card" data-fancybox data-src="#add-movie-'.$movieID.'">';
                                    echo '<figure class="poster">';
                                        echo '<img data-img="'.loadImg('original', $movie->getPoster()).'" alt="" loading="lazy" alt="'.$movie->getTitle().'">';
                                    echo '</figure>';
                                    echo '<span class="title">'.truncate($movie->getTitle(), 30).'</span>';
                                echo '</a>';

                                echo '<div id="add-movie-'.$movieID.'" style="display:none;">';
                                    echo '<p>'.lang_snippet('add_movie').'</p>';
                                    echo '<form method="post" action="/admin/movie/?id='.$id.'">';
                                    echo '<input type="number" name="id" value="'.$movieID.'" style="display:none;">';
                                        echo '<p class="text-right">';
                                            echo '<button type="submit" class="btn btn-success" id="add-movie" name="add-movie">'.lang_snippet('add').'</button>';
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
