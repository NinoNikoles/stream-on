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
                <h1>Einstellungen</h1>
            </div>

            <div class="col12">
                <?php
                    // Get Movie infos
                    if(isset($_POST['get-movie-infos'])) {
                        $id = mysqli_real_escape_string($conn, $_POST['id']);

                        $movie = $tmdb->getMovie($id);
                        $overview = $movie->getOverview();
                        $tagline = $movie->getTagline();
                        $genres = [];
                        $results = $movie->getGenres();
                        foreach ($results as $genre) {
                            $genres[] = $genre->getName();
                        }

                        $genres = implode(", ", $genres);

                        $sql = 'UPDATE movies SET movie_description="'.$overview.'", movie_tagline="'.$tagline.'", movie_genres="'.$genres.'" WHERE movie_tmdbID="'.$id.'"';
                        if (!($conn->query($sql) === TRUE)) {
                            header("Refresh:0");
                        } else {
                            echo '<div>';
                                echo '<div class="col12">';
                                    echo '<p class="text-success">Film hinzugefügt!</p>';
                                echo '</div>';
                            echo '</div>'; 
                        }
                    }

                    if(isset($_POST['change-poster'])) {
                        $id = $_POST['id'];

                        $poster = mysqli_real_escape_string($conn, $_POST['poster']);

                        $sql = 'UPDATE movies SET movie_poster="'.$poster.'" WHERE movie_tmdbID="'.$id.'"';
                        if (!($conn->query($sql) === TRUE)) {
                            header("Refresh:0");
                        } else {
                            echo '<div>';
                                echo '<div class="col12">';
                                    echo '<p class="text-success">Film hinzugefügt!</p>';
                                echo '</div>';
                            echo '</div>'; 
                        }
                    }

                    if(isset($_POST['change-backdrop'])) {
                        $id = $_POST['id'];

                        $backdrop = mysqli_real_escape_string($conn, $_POST['backdrop']);

                        $sql = 'UPDATE movies SET movie_thumbnail="'.$backdrop.'" WHERE movie_tmdbID="'.$id.'"';
                        if (!($conn->query($sql) === TRUE)) {
                            header("Refresh:0");
                        } else {
                            echo '<div>';
                                echo '<div class="col12">';
                                    echo '<p class="text-success">Film hinzugefügt!</p>';
                                echo '</div>';
                            echo '</div>'; 
                        }
                    }
                ?>

                <?php
                    $sql = "SELECT * FROM movies WHERE movie_tmdbID='".$_GET['id']."'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            
                            $id = $_GET['id'];
                            $movie = $tmdb->getMovie($id);
                    
                            $title = $movie->getTitle();
                            $backdrop = $row['movie_thumbnail'];
                            $poster = $row['movie_poster'];       
                            $tagline =  $movie->getTagline();
                            if(!($tagline === '')) {
                                $tagline = ': '.$tagline;
                            }
                            $overview =  $movie->getOverview();
                            $genres = [];
                            $results = $movie->getGenres();
                            foreach ($results as $genre) {
                                $genres[] = $genre->getName();
                            }
                            $genres =  implode(', ', $genres);
                            $rating =  $movie->getVoteAverage();
                            $runtime = $movie->getRuntime();
                            $release = $movie->getReleaseDate();

                            $hours = floor($runtime / 60);
                            $restMinutes = $runtime % 60;
                            
                            if (!($hours == 1)) {
                                $minuteText = lang_snippet('Minutes');
                            } else {
                                $minuteText = lang_snippet('Minute');
                            }

                            if ($hours > 0 ) {
                                if (!($hours > 1)) {
                                    $hourText = lang_snippet('Hour');
                                } else {
                                    $hourText = lang_snippet('Hours');
                                }

                                $finalRuntime = $hours.' '.$hourText.' '. $restMinutes . ' '.$minuteText;
                            } else {
                                $finalRuntime = $restMinutes .' '.$minuteText;
                            }

                            echo '<div class="col7 marg-right-col1">';
                                echo '<p>'.$title.$tagline.'</p>';
                                echo '<p>'.$overview.'</p>';
                                echo '<p>'.$overview.'</p>';
                                echo '<p>'.$genres.'</p>';
                                echo '<p>'.$release.'</p>';
                                echo '<p>'.$finalRuntime.'</p>';
                            echo '</div>';
                        }
                    }

                ?>
                <div class="col4">
                    <div class="col12 marg-bottom-s">
                        <a href="#movie-poster" data-fancybox data-src="#movie-poster">
                            <figure class="poster">
                                <img src="<?php echo $tmdb->getImageURL().$poster;?>">
                            </figure>
                        </a>
                           
                        <div id="movie-poster" style="display:none;">
                            <p>Möchtest du hinzufügen?</p>
                            <form method="post" action="/movies/edit-movie/?id=<?php echo $id;?>">
                                <div class="row">
                                <?php
                                    $allTMDB = new TMDB($cnf);
                                    $allTMDB->setLang('en');
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
                            <form method="post" action="/movies/edit-movie/?id=<?php echo $id;?>">
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
                                <p class="text-right">';
                                    <button type="submit" class="btn btn-success" name="change-backdrop">Hinzufügen</button>
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col12">
                <?php
                    echo '<div class="row">';
                    $collection = $tmdb->getCollection($currentMovieCollection);
                    $movies = $collection->getMovies();

                    foreach ($movies as $movie) {
                        $movieID = $movie->getID();
                        if ($movieID !== intval($currentMovieID)) {
                            echo '<div class="col3 column">';
                                echo '<a href="/movies/edit-movie/?id='.$movieID.'" class="media-card">';
                                    echo '<figure class="poster">';
                                        echo '<img src="'.$tmdb->getImageURL().$movie->getPoster().'" alt="">';
                                    echo '</figure>';
                                    echo '<span class="title">'.$movie->getTitle().'</span>';
                                    /*if (isset($row['tagline'])) {
                                        echo '<span class="title">'.$row['tagline'].'</span>';
                                    }*/
                                echo '</a>';
                            echo '</div>';
                        }
                    }
                    echo '</div>';
                ?>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>