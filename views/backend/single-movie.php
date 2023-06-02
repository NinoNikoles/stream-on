<?php 
    include(ROOT_PATH.'/views/head.php');
    include(ROOT_PATH.'/views/header.php');

    $conn = $mysqli;

    $sql = 'SELECT id FROM movies WHERE movie_tmdbID="'.$_GET['id'].'"';
    $result = $conn->query($sql);
    if (!($result->num_rows > 0)) {
        header('Location: /movies');
    }
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xxl marg-left-col2 marg-right-col4">
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
                        insert_movie($conn, $tmdb, $_POST['id']);
                    }                    
                ?>

                <?php callout(); ?>

                <?php
                    $movie = selectMovieByID($tmdb, $_GET['id']);
                    var_dump($movie);
                    $id = $movie['id'];            
                    $title = $movie['title'];
                    $backdrop = $movie['backdrop'];
                    $poster = $movie['poster'];       
                    $tagline =  $movie['tagline'];   
                    if(!($tagline === '')) {
                        $tagline = '<div class="col12"><p>'.$tagline.'</p></div>';
                    }
                    $test = $tmdb->getMovie($id);
                    $currentMovieCollection = $test->getCollection();
                    var_dump($currentMovieCollection);

                    echo '<div class="col7 marg-right-col1">';
                        echo '<div class="col12"><h1>'.$title.'</h1></div>';
                        echo $tagline;
                        echo '<div class="col12"><p>'.$movie['overview'].'</p></div>';
                        echo '<div class="col3"><p><strong>Bewertung:</strong><br>'.$movie['voteAverage'].'/10</p></div>';
                        echo '<div class="col5"><p><strong>Erscheinungsdatum:</strong><br>'.$movie['release'].'</p></div>';
                        echo '<div class="col4"><p><strong>Dauer:</strong><br>'.$movie['runtime'].'</p></div>';
                        echo '<div class="col12"><p><span><strong>Genre:</strong></span><br>';
                        //$db_genre_ids = json_decode($row['movie_genres']);
                        $genres = $movie['genres'];
                        foreach ($genres as $genre) {
                            echo '<span class="tag">'.$genre['name'].'</span>';
                        }
                        echo '</p></div>';
                    echo '</div>';

                ?>
                <div class="col4">
                    <div class="col12">
                        <?php
                            echo '<a href="#file-list-popup" class="btn" data-fancybox data-src="#file-list-popup">Select movie file</a>';
                                
                            echo '<div id="file-list-popup" style="display:none;">';
                                echo '<div id="file-tree"></div>';
                                echo '<form method="post" action="/movies/edit-movie/?id='.$_GET['id'].'">';
                                    echo '<input type="text" name="moviePath" id="inputMoviePath" value="" style="display:none;">';
                                    echo '<input type="text" name="id" value="'.$_GET['id'].'" style="display:none;">';
                                    echo '<button class="btn" type="submit">Speichern</button>';
                                echo '</form>';
                            echo '</div>';
                        ?>
                    </div>

                    <!--<div class="col12">
                        <?php //output_movie($_GET['id'], false); ?>
                    </div>-->


                    <div class="col12 marg-bottom-s">
                        <a href="#movie-poster" data-fancybox data-src="#movie-poster">
                            <figure class="poster">
                                <img src="<?php echo $tmdb->getImageURL().$poster; ?>">
                            </figure>
                        </a>
                           
                        <div id="movie-poster" style="display:none;">
                            <p>Möchtest du hinzufügen?</p>
                            <form method="post" action="/movies/edit-movie/?id=<?php echo $id; ?>">
                                <div class="row">
                                    <?php
                                        $allTMDB = new TMDB($cnf);
                                        $allTMDB->setLang();
                                        $movie = $allTMDB->getMovie($id);
                                        $moviePosters = $movie->getPosters();
                                        foreach ($moviePosters as $moviePoster) {
                                            $i = 1;
                                            echo '<div class="col3 column">';
                                                echo '<div class="poster-select">';
                                                    echo '<input type="radio" id="poster-'.$i.'" name="poster" value="'.$moviePoster.'">';
                                                    echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                                    echo '<figure class="poster">';
                                                        echo '<img src="'.$tmdb->getImageURL().$moviePoster.'">';
                                                    echo '</figure>';
                                                echo '</div>';
                                            echo '</div>';
                                        }
                                    ?>
                                    </div>
                                
                                <p class="text-right">
                                    <button type="submit" class="btn btn-success" name="change-poster">Hinzufügen</button>
                                </p>
                            </form>
                        </div>
                    </div>

                    <div class="col12">
                        <a href="#movie-backdrop" data-fancybox data-src="#movie-backdrop">
                            <figure class="original">
                                <img src="<?php echo $tmdb->getImageURL().$backdrop;?>">
                            </figure>
                        </a>

                        <div id="movie-backdrop" style="display:none;">
                            <p>Möchtest du hinzufügen?</p>
                            <form method="post" action="/movies/edit-movie/?id=<?php echo $id; ?>">
                            <div class="row">
                                <?php
                                    $allTMDB = new TMDB($cnf);
                                    $allTMDB->setLang('');
                                    $movie = $allTMDB->getMovie($id);
                                    $movieBackdrops = $movie->getBackdrops();
                                    foreach ($movieBackdrops as $movieBackdrop) {
                                        $i = 1;
                                        echo '<div class="col3 column">';
                                            echo '<div class="poster-select">';
                                                echo '<input type="radio" id="backdrop-'.$i.'" name="backdrop" value="'.$movieBackdrop.'">';
                                                echo '<input type="number" name="id" value="'.$id.'" style="display:none;">';
                                                echo '<figure class="original">';
                                                    echo '<img src="'.$tmdb->getImageURL().$movieBackdrop.'">';
                                                echo '</figure>';
                                            echo '</div>';
                                        echo '</div>';
                                    }
                                ?>
                                </div>
                                <p class="text-right">
                                    <button type="submit" class="btn btn-success" name="change-backdrop">Hinzufügen</button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col12">
                <?php
                    if ( !is_null($currentMovieCollection) ) {
                        echo '<div class="row">';
                        $collection = $tmdb->getCollection($currentMovieCollection);
                        $movies = $collection->getMovies();
    
                        foreach ($movies as $movie) {
                            $movieID = $movie->getID();
    
                            if ( getMovieByID($movieID) !== true ) {
                                echo '<div class="col3 column">';
                                    echo '<a href="#add-movie-'.$movieID.'" class="media-card" data-fancybox data-src="#add-movie-'.$movieID.'">';
                                        echo '<figure class="poster">';
                                            echo '<img src="'.$tmdb->getImageURL().$movie->getPoster().'" alt="">';
                                        echo '</figure>';
                                        echo '<span class="title">'.$movie->getTitle().'</span>';
                                    echo '</a>';
    
                                    echo '<div id="add-movie-'.$movieID.'" style="display:none;">';
                                        echo '<p>Möchtest du hinzufügen?</p>';
                                        echo '<form method="post" action="/movies/edit-movie/?id='.$movieID.'">';
                                        echo '<input type="number" name="id" value="'.$movieID.'" style="display:none;">';
                                            echo '<p class="text-right">';
                                                echo '<button type="submit" class="btn btn-success" name="add-movie">Hinzufügen</button>';
                                            echo '</p>';
                                        echo '</form>';
                                    echo '</div>';
                                echo '</div>';
                            }
                        }
                        echo '</div>';
                    }
                    
                ?>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>