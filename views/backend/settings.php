<?php 
    $pageTitle = pageTitle(lang_snippet(('settings')));
    include(ROOT_PATH.'/views/header.php');

    $conn = dbConnect();
    $cnf = tmdbConfig();

    $siteTitle = $cnf['site_title'];
    $apikey = $cnf['apikey'];
    $apiLang = $cnf['lang'];
    $checked = $cnf['enable_edit_btn'];

    // Benutzeranmeldung
    if(isset($_POST['save-settings'])) {
        updateSettings($_POST);
    }

    $siteTitle = $siteTitle;
    $apikey = $apikey;
    $apiLang = $apiLang;
    $checked = $checked;
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">

        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('settings'); ?></h1>
            </div>
            <div class="col12">
                <?php callout(); ?>
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
                        <span class="smaller"><?php echo lang_snippet('apikey_info'); ?></span>
                    </p>
                </div>
                <div class="col12 column">
                    <p>
                        <label for="language"><?php echo lang_snippet('language');?>*
                            <input type="text" name="language" id="language" placeholder="Example: 'en-US'" maxlength="5" value="<?php echo $apiLang; ?>" required>
                        </label>
                        <span class="smaller"><?php echo lang_snippet('lang_info'); ?></span>
                    </p>
                </div>
                <div class="col12 column">
                    <p>
                        <lable for="enable-edit" class="checkbox-label"><?php echo lang_snippet('enable_edit_btn'); ?>
                            <input type="checkbox" id="enable-edit" name="enable-edit" <?php echo $checked; ?>>
                        </lable>
                    </p>
                </div>
                <div class="col12 column text-right marg-top-base">
                    <button class="btn btn-small btn-success icon-left icon-save" type="submit" name="save-settings"><?php echo lang_snippet('save'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include('views/footer.php'); ?>