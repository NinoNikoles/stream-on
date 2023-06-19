<?php include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
$tmdb = setupTMDB();

$sql = "SELECT * FROM genres ORDER BY genre_id ASC";
$results = $conn->query($sql);
?>

<div class="innerWrap marg-top-l" id="searchpage">
    <div class="col12">
        <h2>Suchergebnisse für: <span class="h2 marg-no" id="search-value"><?php if (isset($_POST['search'])) { echo $_POST['search']; } ?></span></h1>
    </div>
    <div class="col12" id="moviePageLivesearchResults">
        <?php
            if ( isset($_POST['search']) && $_POST['search'] !== '' ) {
                $movies = selectMovieByTitle($_POST['search']);
                foreach ( $movies as $movie ) {    
                    $movieID = $movie['id'];
                    $movieTitle = $movie['title'];
                    $movieOverview = $movie['overview'];
                    $movieRating = $movie['voteAverage'];
                    $movieRuntime = $movie['runtime'];
                    $movieRelease = new DateTime($movie['release']);
                    $releaseYear = $movieRelease->format('Y');
                    $moviePoster = $movie['poster'];
                    $movieBackdrop = $movie['backdrop'];
                    $genres = $movie['genres'];
                    $genreHTML = '';
                    foreach ( $genres as $genre ) {
                        $genreHTML = $genreHTML . '<span class="tag">'.$genre['name'].'</span>';
                    }
        
                    echo '<a href="#modal-'.$movieID.'" title="'.$movieTitle.'" class="media-card" data-modal data-src="#content-'.$movieID.'">';
                        echo '<figure class="poster">';
                            echo '<img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">';
                        echo '</figure>';
                        echo '<span class="title">'.truncate($movieTitle,20).'</span>';
                    echo '</a>';
            
                    echo '<div class="info-popup" id="content-'.$movieID.'" style="display:none;">';
                        echo '<div class="col12 marg-bottom-xs mobile-only">';
                            echo '<figure class="widescreen">';
                                echo '<img src="'.$tmdb->getImageURL().$movieBackdrop.'">';
                            echo '</figure>';
                        echo '</div>';
                        echo '<div class="innerWrap">';
                            echo '<div class="col7 marg-right-col1">';
                                echo '<p class="h2">'.$movieTitle.'</p>';
                                echo '<p class="small">';
                                    echo '<span class="tag">'.$releaseYear.'</span>';
                                    echo '<span class="tag">'.$movieRating.'/10</span>';
                                    echo '<span class="tag">'.runtimeToString($movieRuntime).'</span>';
                                echo '</p>';
                                echo '<a href="/watch/?id='.$movieID.'" class="btn btn-white icon-left icon-play">Jetzt schauen</a>';
                                echo '<p class="small">'.$movieOverview.'</p>';
                                echo '<p class="small">'.$genreHTML.'</p>';
                            echo '</div>';
                            echo '<div class="col4 desktop-only">';
                                echo '<figure class="poster">';
                                    echo '<img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">';
                                echo '</figure>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                }
            }
        ?>
    </div>
</div>


<?php include('views/footer.php'); ?>