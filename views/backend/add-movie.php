<?php 
    include(ROOT_PATH.'/views/includes/head.php');
    include(ROOT_PATH.'/admin/config.php');
    include(ROOT_PATH.'/views/header.php');

    $conn = $mysqli;

?>

<div class="col12">
    <div class="col2 sidebar">
        <ul>
            <li><a href="/settings"><?php echo lang_snippet('settings'); ?></a></li>
            <li><a href="/users"><?php echo lang_snippet('users'); ?></a></li>
            <li><a href="/movies"><?php echo lang_snippet('movies'); ?></a></li>
            <li><a href="/shows"><?php echo lang_snippet('shows'); ?></a></li>
        </ul>
    </div>
    <div class="innerWrap">
        
        <div class="col8 marg-top-xxl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1>Einstellungen</h1>
            </div>




        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>