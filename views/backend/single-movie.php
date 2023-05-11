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
                        var_dump($movie);

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
                ?>

                <?php
                    $sql = "SELECT * FROM movies WHERE movie_tmdbID='".$_GET['id']."'";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            
                            $currentMovieID = $row['movie_tmdbID'];
                            $currentMovieCollection = $row['movie_collection'];
                            $runtime = $row['movie_length'];

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

                            echo '<form>';
                                echo '<div class="row">';
                                echo '<div class="col2 column"><p>';
                                    echo '<label for="movie-id">ID';
                                        echo '<input type="number" name="movie-id" value="'.$row['movie_tmdbID'].'" disabled>';
                                    echo '</label>';
                                echo '</p></div>';

                                echo '<div class="col5 column"><p>';
                                    echo '<label for="movie-title">Title';
                                        echo '<input type="text" name="movie-title" value="'.$row['movie_name'].'" disabled>';
                                    echo '</label>';
                                echo '</p></div>';

                                echo '<div class="col5 column"><p>';
                                    echo '<label for="movie-title">Tagline';
                                        echo '<input type="text" name="movie-tagline" value="'.$row['movie_tagline'].'" disabled>';
                                    echo '</label>';
                                echo '</p></div>';

                                echo '<div class="col12 column"><p>';
                                    echo '<label for="movie-description">Description';
                                        echo '<textarea name="movie-description" disabled>';
                                            echo $row['movie_description'];
                                        echo '</textarea>';
                                    echo '</label>';
                                echo '</p></div>';

                                echo '<div class="col6 column"><p>';
                                    echo '<label for="movie-genres">Genres';
                                        echo '<input type="text" name="movie-genres" value="'.$row['movie_genres'].'" disabled>';
                                    echo '</label>';
                                echo '</p></div>';

                                echo '<div class="col3 column"><p>';
                                    echo '<label for="movie-genres">Release';
                                        echo '<input type="text" name="movie-genres" value="'.$row['movie_release'].'" disabled>';
                                    echo '</label>';
                                echo '</p></div>';

                                echo '<div class="col3 column"><p>';
                                    echo '<label for="movie-genres">Length';
                                        echo '<input type="text" name="movie-genres" value="'.$finalRuntime.'" disabled>';
                                    echo '</label>';
                                echo '</p></div>';

                                echo '</div>';
                            echo '</form>';
                        }
                    }

                ?>
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