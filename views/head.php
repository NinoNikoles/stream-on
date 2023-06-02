<?php
require_once ROOT_PATH.'/tmdb/configuration/default.php';
require_once ROOT_PATH.'/tmdb/tmdb-api.php';
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
$tmdb = new TMDB($cnf);
?>

<!DOCTYPE html>
<html lang="<?php echo get_browser_language(); ?>" <?php setTheme();?> >
<head>
    <meta charset="UTF-8">
    <link rel="icon" href="/favicon.ico">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="/build/style.min.css">
    <link rel="stylesheet" type="text/css" href="/build/font.min.css">

    <title>Vite App</title>
</head>
<body>