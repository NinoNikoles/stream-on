<?php
include(ROOT_PATH.'/views/head.php');
include(ROOT_PATH.'/views/header.php');

//-- Main Content --
?>

<?php
$genreSQL = "SELECT * FROM genres";
$results = $conn->query($genreSQL);
if ($results->num_rows > 0) {
    while ($row = $results->fetch_assoc()) {
        $movieSQL = "SELECT * FROM movies";
        $resultsMovies = $conn->query($movieSQL);

        if ($resultsMovies->num_rows > 0) {
            while ($rowMovie = $resultsMovies->fetch_assoc()) {
                $movie = $tmdb->getMovie($rowMovie['movie_tmdbID']);
                $genres = $movie->getGenres();

                foreach ( $genres as $genre ) {
                    if ($genre->getID() == $row['genre_id']) {
                        echo '<div class="col12">';
                        echo '<h3>'.$genre->getName().'</h3>';
                        echo '<div class="col2">';
                            echo '<span>'.$movie->getTitle().'</span>';
                        echo '</div>';
                    }
                }
            }
        }
    }
}
?>




<?php
//------------------
include(ROOT_PATH.'/views/footer.php');
?>