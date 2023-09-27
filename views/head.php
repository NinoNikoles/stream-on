<?php 
$conn = dbConnect();
?>
<!DOCTYPE html>
<html lang="<?php echo get_browser_language(); ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php loadFavicon(); ?>
    <link rel="stylesheet" type="text/css" href="/views/build/style.min.css">
    <link rel="stylesheet" type="text/css" href="/views/build/font.min.css">
    <link rel="manifest" crossorigin="use-credentials" href="/manifest.json"/>
    <title><?php echo $pageTitle[0] /*.$pageTitle[1]*/;?></title>
</head>
<body>