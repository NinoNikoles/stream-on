<?php 
$conn = dbConnect();

if ( !($pageTitle[0] === 'Install') ) {
    loggedInCheck();
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
    <link rel="manifest" href="/site.webmanifest">
    <title><?php echo $pageTitle[0] /*.$pageTitle[1]*/;?></title>
</head>
<body>