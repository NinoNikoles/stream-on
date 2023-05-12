<?php 
    include(ROOT_PATH.'/views/head.php');
    include(ROOT_PATH.'/views/header.php');

    $conn = $mysqli;
            
    // Add Movie
    if (isset($_POST['id'])) {
        insert_movie($conn, $tmdb, $_POST['id']);
    }
    
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xxl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('Movies'); ?></h1>
            </div>

            <div class="col12 marg-bottom-m">
                <div id="searchbar">
                    <label for="movie-name">Film Name*
                        <input type="text" id="movie-search" name="movie-name" placeholder="Film Name" value="" required>
                    </label>
                    <div id="movieSearchResults" class="hidden"></div>      
                </div>
            </div>

            <div class="col12">
                <?php callout(); ?>
            </div>

            <div class="row">
                <?php 
                    $sql = "SELECT * FROM movies ORDER BY movie_title";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '<div class="col3 column">';
                                echo '<a href="/movies/edit-movie/?id='.$row['movie_tmdbID'].'" class="media-card">';
                                    echo '<figure class="poster">';
                                        echo '<img src="'.$tmdb->getImageURL().$row['movie_poster'].'" alt="">';
                                    echo '</figure>';
                                    echo '<span class="title">'.$row['movie_title'].'</span>';
                                echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col12 column">';
                            echo '<div class="callout warning">';
                                echo '<p>'.lang_snippet('No movies available').'</p>';
                            echo '</div>';
                        echo '</div>';
                    }
                ?>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>