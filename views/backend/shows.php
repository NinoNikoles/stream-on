<?php
include(ROOT_PATH.'/views/header.php');
$conn = dbConnect();
$tmdb = setupTMDB();
            
// Add Movie
if ( isset($_POST['showSubmit']) ) {
    insertShow($_POST['id']);
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

            <div class="grid-row">
                <?php
                    echo '<form method="post" action="/admin/shows">';
                        echo '<input type="number" name="id" value="1100" style="display:none;">';
                        echo '<button class="btn btn-small btn-success" id="showSubmit" name="showSubmit" type="submit">'.lang_snippet('save').'</button>';
                    echo '</form>';
                ?>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>