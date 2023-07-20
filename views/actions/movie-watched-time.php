<?php
    $conn = dbConnect(); 
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');

    $userID = intval($_SESSION['userID']);
    $mediaID = intval($_POST['mediaID']);
    $watchedTime = floatval($_POST['time']);
    $totalLength = floatval($_POST['totalLength']);
    $watchedInPercent = ($watchedTime/$totalLength)*100;
    echo $_POST['showID'];
    if ( isset($_POST['showID']) ) {
        $showID = $_POST['showID'];
    } else {
        $showID = NULL;
    }

    if ( $watchedInPercent === 100 ) {
        $sql = "INSERT INTO media_watched(user_id, media_id, show_id, watched_seconds, total_length, watched) VALUES
        ($userID, $mediaID, $showID, $watchedTime, $totalLength, 1)
        ON DUPLICATE KEY UPDATE watched_seconds = VALUES(watched_seconds)";
    } else {
        $sql = "INSERT INTO media_watched(user_id, media_id, show_id, watched_seconds, total_length) VALUES
        ($userID, $mediaID, $showID, $watchedTime, $totalLength)
        ON DUPLICATE KEY UPDATE watched_seconds = VALUES(watched_seconds)";
    }

    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
?>
