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
                    $sql = "SELECT movies.movie_tmdbID, movies.movie_title, highlights.highlight_status
                    FROM movies
                    INNER JOIN highlights ON movies.movie_tmdbID=highlights.movie_id";

                    $results = $conn->query($sql);
                    while($row = $results->fetch_assoc()) {
                        
                        if (!($row['highlight_status'] === NULL) && ($row['highlight_status'] > 0)) {
                            $checked = "checked";
                        } else {
                            $checked = "";
                        }

                        echo '<tr>';
                            echo '<td>'.$row['movie_title'].'</td>';
                            echo '<td><input type="checkbox" data-movie="'.$row['movie_tmdbID'].'" class="highlight-change" '.$checked.'></td>';
                            echo '<td><button data-src="#delete-highlight-'.$row['movie_tmdbID'].'" class="btn btn-small btn-alert icon-only icon-trash marg-no" data-fancybox></button></td>';
                        echo '</tr>';

                        echo '<div id="delete-highlight-'.$row['movie_tmdbID'].'" style="display:none;">';
                            echo '<p>Möchtest du den Nutzer <strong>"'.$row['movie_title'].'"</strong> wirklich löschen?</p>';
                            echo '<form method="post" action="/admin/highlights">';
                                echo '<p class="text-right marg-no">';
                                    echo '<input type="number" name="highlight" id="'.$row['movie_tmdbID'].'" value="'.$row['movie_tmdbID'].'" style="display:none;" required>';
                                    echo '<button class="btn btn-alert" type="submit" name="delete-highlight">'.lang_snippet('delete').'</button>';
                                echo '</p>';
                            echo '</form>';
                        echo '</div>';
                    }

                ?>
            </table>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>