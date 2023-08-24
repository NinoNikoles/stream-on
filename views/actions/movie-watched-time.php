<?php
    $conn = dbConnect(); 
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');

    $userID = intval($_SESSION['userID']);
    $mediaID = intval($_POST['mediaID']);
    $watchedTime = floatval($_POST['time']);
    $watched = intval($_POST['watched']);
    $totalLength = floatval($_POST['totalLength']);
    $watchedInPercent = ($watchedTime/$totalLength)*100;

    if ( isset($_POST['show']) ) {
        $showID = $_POST['show'];
    } else {
        $showID = NULL;
    }

    if ( $watched === 1 ) {
        $watchedTime = 0;
    }
    
    $sql = "INSERT INTO media_watched(user_id, media_id, show_id, watched_seconds, total_length, watched) VALUES
    ($userID, $mediaID, $showID, $watchedTime, $totalLength,  $watched)
    ON DUPLICATE KEY UPDATE watched_seconds = VALUES(watched_seconds), watched = VALUES(watched)";

    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
?>
