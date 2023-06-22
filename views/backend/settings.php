<?php 
    include(ROOT_PATH.'/views/header.php');

    $conn = dbConnect();
    $cnf = tmdbConfig();

    $siteTitle = $cnf['site_title'];
    $apikey = $cnf['apikey'];
    $apiLang = $cnf['lang'];

    // Benutzeranmeldung
    if(isset($_POST['save-settings'])) {
        updateSettings($_POST);
    }

    $siteTitle = $siteTitle;
    $apikey = $apikey;
    $apiLang = $apiLang;

?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('settings'); ?></h1>
            </div>
            <form method="post" action="/admin/settings" class="row">
                 <div class="col12 column">
                    <p>
                        <label for="site_title"><?php echo lang_snippet('page_title'); ?>*
                            <input type="text" name="site_title" id="site_title" placeholder="<?php echo lang_snippet('page_title'); ?>" value="<?php echo $siteTitle; ?>"required>
                        </label>
                    </p>
                </div>
                <div class="col12 column">
                    <p>
                        <label for="apikey"><?php echo lang_snippet('api_key'); ?>*
                            <input type="text" name="apikey" id="apikey" placeholder="<?php echo lang_snippet('api_key'); ?>" value="<?php echo $apikey; ?>"required>
                        </label>
                    </p>
                </div>
                <div class="col12 column">
                    <p>
                        <label for="language"><?php echo lang_snippet('language'); ?>
                            <input type="text" name="language" id="language" placeholder="Example: 'en,de'" value="<?php echo $apiLang; ?>" required>
                        </label>
                    </p>
                </div>
                <div class="col12 column text-right">
                    <button class="btn btn-primary" type="submit" name="save-settings"><?php echo lang_snippet('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('views/footer.php'); ?>