<?php
    $conn = dbConnect(); 
    include(ROOT_PATH.'/src/tmdb/configuration/default.php');
    include(ROOT_PATH.'/src/tmdb/tmdb-api.php');

    $userID = intval($_SESSION['userID']);
    $volume = floatval($_POST['volume']);
    $volume = round($volume,2);
    
    $sql = "UPDATE users SET media_volume = $volume WHERE id = $userID;";

    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
?>
