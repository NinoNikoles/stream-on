<?php
function videoPlayer($movieID, $fullscreen = false) {
    $tmdb = setupTMDB();
    $conn = dbConnect();
    $sql = "SELECT id, movie_file_path, movie_overview, movie_thumbnail FROM movies WHERE movie_tmdbID='$movieID'";
    $filePath = $conn->query($sql)->fetch_assoc()['movie_file_path'];
    $id = $conn->query($sql)->fetch_assoc()['id'];
    $info = $conn->query($sql)->fetch_assoc()['movie_overview'];
    $backdrop = $conn->query($sql)->fetch_assoc()['movie_thumbnail'];

    if ( $filePath !== "" ) {
        $userID = $_SESSION['userID'];
        $sqlTime = "SELECT watched_seconds FROM movie_watched WHERE user_id='$userID ' and movie_id='$movieID'";
        if($conn->query($sqlTime)->num_rows > 0) {
            $watchedTime = $conn->query($sqlTime)->fetch_assoc()['watched_seconds'];
        } else {
            $watchedTime = 0;
        }
        

        if($fullscreen === true) {
            echo '<figure>';
                echo '<video id="player" class="video-js" data-id="'.$movieID.'" data-set="fullscreen" data-fullscreen="true" data-sound="true" controls preload="auto" poster="'.$tmdb->getImageURL().$backdrop.'">'; //'.$tmdb->getImageURL().$backdrop.' // data-current-time="true" data-duration="true"
                    echo '<source src="'.$filePath.'" type="video/mp4"/>';
                echo '</video>';
                echo '<button id="player-back-btn" title="Back" onclick="history.back()"></button>';
            echo '</figure>';
            //
        } else {
            echo '<figure class="widescreen">';
                echo '<video id="player" class="video-js" data-id="'.$movieID.'" data-sound="true" data-fullscreen="true" controls preload="auto" poster="'.$tmdb->getImageURL().$backdrop.'">'; //'.$tmdb->getImageURL().$backdrop.'
                    echo '<source src="'.$filePath.'" type="video/mp4" />';
                echo '</video>';
            echo '</figure>';
        }
        echo '<span data-time="'.$watchedTime.'" id="time"></span>';
    }

    $conn->close();
}
?>