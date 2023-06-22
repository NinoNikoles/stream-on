<?php
    $conn = dbConnect(); 
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');
    var_dump($_POST);

    $userID = intval($_SESSION['userID']);
    $movieID = intval($_POST['movieID']);
    $watchedTime = floatval($_POST['time']);
    var_dump($watchedTime);

    $sql = "INSERT INTO movie_watched(user_id, movie_id, watched_seconds) VALUES
    ('$userID', '$movieID', '$watchedTime')
    ON DUPLICATE KEY UPDATE watched_seconds = VALUES(watched_seconds)";
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
?>