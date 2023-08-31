<?php
$pageTitle = pageTitle(lang_snippet(('movies')));
include(ROOT_PATH.'/views/header.php');
$conn = dbConnect();
$tmdb = setupTMDB();
            
// Add Movie
if ( isset($_POST['add-movie']) ) {
    insertMovie($_POST['id']);
}
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('movies'); ?></h1>
            </div>

            <div class="col12 marg-bottom-m">
                <div id="searchbar">
                    <label for="movie-api-search">Film Name*
                        <input type="text" id="movie-api-search" name="movie-name" placeholder="<?php echo lang_snippet('movie_title'); ?>" value="" required>
                    </label>
                    <div id="movieSearchResults" class="hidden"></div>      
                </div>
            </div>

            <div class="col12">
                <?php callout(); ?>
            </div>

            <div class="row">
                <?php 
                    $movies = selectAllMoviesByTitle('ASC');
                    
                    if ( $movies > 0 ) {
                        foreach ( $movies as $movie ) {
                            echo '<div class="col-6 col-3-medium column">';
                                echo '<a href="/admin/movie/?id='.$movie['tmdbID'].'" title="'.$movie['title'].'" class="media-card">';
                                    echo '<figure class="poster">';
                                        echo '<img data-img="'.loadImg('original', $movie['poster']).'" alt="" loading="lazy" alt="'.$movie['title'].'">';
                                    echo '</figure>';
                                    echo '<span class="title marg-no">'.truncate($movie['title'],15).'</span>';
                                echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="col12 column">';
                            echo '<div class="callout warning">';
                                echo '<p>'.lang_snippet('no_movies_available').'</p>';
                            echo '</div>';
                        echo '</div>';
                    }
                ?>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>