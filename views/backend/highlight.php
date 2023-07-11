<?php
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

            <div class="col12">
                <?php callout(); ?>
            </div>

        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>