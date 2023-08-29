<?php 
$siteTitle = getSiteTitle();

if( $_SESSION > 0 && !isset($_SESSION['logged_in']) || $_SESSION > 0 && $_SESSION['logged_in'] !== true ) {
    destroySesssion();
    if ( !pageCheck("/login") ) {
        page_redirect("/login");
    }
}
?>
<!DOCTYPE html>
<html lang="<?php echo get_browser_language(); ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php loadFavicon(); ?>
    <link rel="stylesheet" type="text/css" href="/views/build/style.min.css">
    <link rel="stylesheet" type="text/css" href="/views/build/font.min.css">
    <title><?php echo $siteTitle;?></title>
</head>
<body>