<?php
include(ROOT_PATH.'/views/header.php');
            
// Add Movie
if ( isset($_POST['delete-highlight']) ) {
    deleteHighlight($_POST['highlight']);
}
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('highlights'); ?></h1>
            </div>

            <div class="col12">
                <?php callout(); ?>
            </div>

            <table>
                <thead>
                    <th><?php echo lang_snippet('name'); ?></th>
                    <th><?php echo lang_snippet('status'); ?></th>
                    <th><?php echo lang_snippet('delete'); ?></th>
                </thead>
                <?php
                    $conn = dbConnect();
                    $hightlightSelect = "SELECT highlights.highlight_id, highlight_status, media.type FROM highlights INNER JOIN media ON highlights.highlight_id = media.tmdbID AND media.tmdbID IN (SELECT movies.movie_tmdbID FROM movies UNION SELECT shows.show_tmdbID FROM shows)";
                    $hightlightResult = $conn->query($hightlightSelect);

                    if ( $hightlightResult->num_rows > 0) {
                        while ( $highlight = $hightlightResult->fetch_assoc() ) {

                            if (!($highlight['highlight_status'] === NULL) && ($highlight['highlight_status'] > 0)) {
                                $checked = "checked";
                            } else {
                                $checked = "";
                            }

                            if ( $highlight['type'] === 'movie' ) {
                                $mediaID = $highlight['highlight_id'];
                                $getMovie = "SELECT movie_tmdbID, movie_title FROM movies WHERE movie_tmdbID=$mediaID";
                                $movieResults = $conn->query($getMovie);
                
                                while ( $movie = $movieResults->fetch_assoc() ) {
                                    $movieID = $movie['movie_tmdbID'];
                                    $title = $movie['movie_title'];
                                }
                            } else {
                                $showID = $highlight['highlight_id'];
                                $getShow = "SELECT show_tmdbID, show_title FROM shows WHERE show_tmdbID=$mediaID";
                                $showResults = $conn->query($getShow);
                
                                while ( $show = $showResults->fetch_assoc() ) {
                                    $mediaID = $show['show_tmdbID'];
                                    $title = $show['show_title'];
                                }
                            }

                            echo '<tr>';
                                echo '<td>'.$title.'</td>';
                                echo '<td><input type="checkbox" data-media="'.$mediaID.'" class="highlight-change" '.$checked.'></td>';
                                echo '<td><button data-src="#delete-highlight-'.$mediaID.'" class="btn btn-small btn-alert icon-only icon-trash marg-no" data-fancybox></button></td>';
                            echo '</tr>';

                            echo '<div id="delete-highlight-'.$mediaID.'" style="display:none;">';
                                echo '<p>Möchtest du den Nutzer <strong>"'.$title.'"</strong> wirklich löschen?</p>';
                                echo '<form method="post" action="/admin/highlights">';
                                    echo '<p class="text-right marg-no">';
                                        echo '<input type="number" name="highlight" id="'.$mediaID.'" value="'.$mediaID.'" style="display:none;" required>';
                                        echo '<button class="btn btn-alert" type="submit" name="delete-highlight">'.lang_snippet('delete').'</button>';
                                    echo '</p>';
                                echo '</form>';
                            echo '</div>';
                        }
                    }
                ?>
            </table>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>