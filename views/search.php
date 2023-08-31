<?php 
$pageTitle = pageTitle(lang_snippet(('search')));
include(ROOT_PATH.'/views/header.php');

$conn = dbConnect();
?>

<div class="innerWrap marg-top-l marg-bottom-l" id="searchpage">
    <div class="col12">
        <h2>Suchergebnisse für: <span class="h2 marg-no" id="search-value"><?php if (isset($_POST['search'])) { echo $_POST['search']; } ?></span></h1>
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


<?php include('views/footer.php'); ?>