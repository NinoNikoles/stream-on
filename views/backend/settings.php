<?php 
    include(ROOT_PATH.'/views/head.php');
    include(ROOT_PATH.'/views/header.php');

    $conn = dbConnect();
    $cnf = tmdbConfig();

    $siteTitle = $cnf['site_title'];
    $apikey = $cnf['apikey'];
    $apiLang = $cnf['lang'];

    // Benutzeranmeldung
    if(isset($_POST['save-settings'])) {
        $siteTitle = mysqli_real_escape_string($conn, $_POST['site_title']);
        $apikey = mysqli_real_escape_string($conn, $_POST['apikey']);
        $apiLang = mysqli_real_escape_string($conn, $_POST['language']);

        $sql = 'UPDATE settings SET setting_option="'.$siteTitle.'" WHERE setting_name="site_title"';
        if (!($conn->query($sql) === TRUE)) {
            die('Error creating table: ' . $conn->error);
        }

        $sql = 'UPDATE settings SET setting_option="'.$apikey.'" WHERE setting_name="apikey"';
        if (!($conn->query($sql) === TRUE)) {
            die('Error creating table: ' . $conn->error);
        }

        $sql = 'UPDATE settings SET setting_option="'.$apiLang.'" WHERE setting_name="apilang"';
        if (!($conn->query($sql) === TRUE)) {
            echo '<div class="innerWrap">';
                echo '<div class="col4 marg-left-col4">';
                    echo '<p class="text-alert">'.lang_snippet('failed_to_save').'</p>';
                echo '</div>';
            echo '</div>';
        }

        page_redirect('/admin/settings');
    }

    $siteTitle = $siteTitle;
    $apikey = $apikey;
    $apiLang = $apiLang;

?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xxl marg-left-col2 marg-right-col4">
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