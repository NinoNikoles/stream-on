<div id="searchpage" class="hidden">
    <a href="#" id="close-search" class="icon icon-close"></a>
    <div class="innerWrap marg-top-xl marg-bottom-xl">
        <div class="col12">
            <h1><?php echo lang_snippet('search_result'); ?> <span class="h1 marg-no" id="search-value"><?php if (isset($_POST['search'])) { echo $_POST['search']; } ?></span></h1>
        </div>
        <div class="col12" id="moviePageLivesearchResults">
            <div class="grid-row" id="moviePageLivesearchResults">
                <?php
                    if ( isset($_POST['search']) && $_POST['search'] !== '' ) {
                        $movies = selectMovieByTitle($_POST['search']);
                        foreach ( $movies as $movie ) {    
                            echo media_card($movie, 'col-6 col-4-xsmall col-2-medium grid-padding');
                        }
                    }
                ?>
            </div>
        </div>
    </div>
</div>