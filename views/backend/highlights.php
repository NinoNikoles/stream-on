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
                <tbody>
                <?php
                    $conn = dbConnect();
                    $hightlightSelect = "SELECT highlights.highlight_id, highlight_status, media.title 
                    FROM highlights INNER JOIN media ON highlights.highlight_id=media.tmdbID AND media.tmdbID";
                    $hightlightResult = $conn->query($hightlightSelect);

                    if ( $hightlightResult->num_rows > 0) {
                        while ( $highlight = $hightlightResult->fetch_assoc() ) {
                            
                            $mediaID = $highlight['highlight_id'];
                            $title = $highlight['title'];
                            
                            if (!($highlight['highlight_status'] === NULL) && ($highlight['highlight_status'] > 0)) {
                                $checked = "checked";
                            } else {
                                $checked = "";
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
                </tbody>
            </table>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>