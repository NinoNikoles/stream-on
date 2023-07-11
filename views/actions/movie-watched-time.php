<?php
    $conn = dbConnect(); 
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');

    $userID = intval($_SESSION['userID']);
    $movieID = intval($_POST['movieID']);
    $watchedTime = floatval($_POST['time']);
    $totalLength = floatval($_POST['totalLength']);
    $watchedInPercent = ($watchedTime/$totalLength)*100;

    if ( $watchedInPercent === 100 ) {
        $sql = "INSERT INTO movie_watched(user_id, movie_id, watched_seconds, total_length, watched) VALUES
        ($userID, $movieID, $watchedTime, $totalLength, 1)
        ON DUPLICATE KEY UPDATE watched_seconds = VALUES(watched_seconds)";
    } else {
        $sql = "INSERT INTO movie_watched(user_id, movie_id, watched_seconds, total_length) VALUES
        ($userID, $movieID, $watchedTime, $totalLength)
        ON DUPLICATE KEY UPDATE watched_seconds = VALUES(watched_seconds)";
    }

    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
?>
