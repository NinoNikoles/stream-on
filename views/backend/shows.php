<?php
include(ROOT_PATH.'/views/header.php');
$conn = dbConnect();
$tmdb = setupTMDB();
            
// Add Movie
if ( isset($_POST['add-show']) ) {
    insertShow($_POST['id']);
}
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('shows'); ?></h1>
            </div>

            <div class="col12 marg-bottom-m">
                <div id="searchbar">
                    <label for="show-api-search">Film Name*
                        <input type="text" id="show-api-search" name="show-name" placeholder="<?php echo lang_snippet('title'); ?>" value="" required>
                    </label>
                    <div id="showSearchResults" class="hidden"></div>      
                </div>
            </div>

            <div class="col12">
                <?php callout(); ?>
            </div>

            <div class="row">
                <?php 
                    $shows = selectAllShowsByTitle('ASC');
                    if ( $shows > 0 ) {
                        foreach ( $shows as $show ) {
                            echo '<div class="col-6 col-3-medium column">';
                                echo '<a href="/admin/show/?id='.$show['tmdbID'].'" title="'.$show['title'].'" class="media-card">';
                                    echo '<figure class="poster">';
                                        echo '<img data-img="'.loadImg('original', $show['poster']).'" alt="" loading="lazy" alt="'.$show['title'].'">';
                                    echo '</figure>';
                                    echo '<span class="title">'.truncate($show['title'],20).'</span>';
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