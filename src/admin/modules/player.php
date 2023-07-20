<?php
function movieVideoPlayer($movieID, $fullscreen = false) {
    $conn = dbConnect();
    $sql = "SELECT id, file_path, overview, backdrop FROM media WHERE tmdbID='$movieID' AND mediaType='movie'";
    $filePath = $conn->query($sql)->fetch_assoc()['file_path'];
    $id = $conn->query($sql)->fetch_assoc()['id'];
    $info = $conn->query($sql)->fetch_assoc()['overview'];
    $backdrop = $conn->query($sql)->fetch_assoc()['backdrop'];

    if ( $filePath !== "" ) {
        $userID = $_SESSION['userID'];
        $sqlTime = "SELECT watched_seconds FROM media_watched WHERE user_id='$userID' AND media_id='$movieID'";
        if($conn->query($sqlTime)->num_rows > 0) {
            $watchedTime = $conn->query($sqlTime)->fetch_assoc()['watched_seconds'];
        } else {
            $watchedTime = 0;
        }
        

        if($fullscreen === true) {
            echo '<figure>';
                echo '<video id="player" class="video-js" data-id="'.$movieID.'" data-set="fullscreen" data-fullscreen="true" data-sound="true" data-current-time="true" data-duration="true" controls preload="auto" poster="'.loadImg('original', $backdrop).'">'; //'.$tmdb->getImageURL().$backdrop.' // 
                    echo '<source src="'.$filePath.'" type="video/mp4"/>';
                echo '</video>';
                echo '<button id="player-back-btn" title="Back" onclick="history.back()"></button>';
            echo '</figure>';
            //
        } else {
            echo '<figure class="widescreen">';
                echo '<video id="player" class="video-js" data-id="'.$movieID.'" data-sound="true" data-fullscreen="true" controls preload="auto" poster="'.loadImg('original', $backdrop).'">'; //'.$tmdb->getImageURL().$backdrop.'
                    echo '<source src="'.$filePath.'" type="video/mp4" />';
                echo '</video>';
            echo '</figure>';
        }
        echo '<span data-time="'.$watchedTime.'" id="time"></span>';
    }

    $conn->close();
}

function showVideoPlayer($episodeID, $showID, $fullscreen = false) {
    $conn = dbConnect();
    $sql = "SELECT id, file_path, overview, backdrop FROM episodes WHERE tmdbID=$episodeID AND show_id=$showID;";

    $episodeResult = $conn->query($sql);
    if ( $episodeResult->num_rows > 0 ) {
        while ( $episode = $episodeResult->fetch_assoc() ) {
            $filePath = $episode['file_path'];
            $id = $episode['id'];
            $info = $episode['overview'];
            $backdrop = $episode['backdrop'];
        }
    }
    


    if ( $filePath !== "" ) {
        $userID = $_SESSION['userID'];
        $sqlTime = "SELECT watched_seconds FROM media_watched WHERE user_id='$userID' AND media_id='$episodeID'";
        if($conn->query($sqlTime)->num_rows > 0) {
            $watchedTime = $conn->query($sqlTime)->fetch_assoc()['watched_seconds'];
        } else {
            $watchedTime = 0;
        }
        

        if($fullscreen === true) {
            echo '<figure>';
                echo '<video id="player" class="video-js" data-id="'.$episodeID.'" data-set="fullscreen" data-fullscreen="true" data-sound="true" data-current-time="true" data-duration="true" controls preload="auto" poster="'.loadImg('original', $backdrop).'">'; //'.$tmdb->getImageURL().$backdrop.' // 
                    echo '<source src="'.$filePath.'" type="video/mp4"/>';
                echo '</video>';
                echo '<button id="player-back-btn" title="Back" onclick="history.back()"></button>';
            echo '</figure>';
            //
        } else {
            echo '<figure class="widescreen">';
                echo '<video id="player" class="video-js" data-id="'.$episodeID.'" data-sound="true" data-fullscreen="true" controls preload="auto" poster="'.loadImg('original', $backdrop).'">'; //'.$tmdb->getImageURL().$backdrop.'
                    echo '<source src="'.$filePath.'" type="video/mp4" />';
                echo '</video>';
            echo '</figure>';
        }
        echo '<span data-time="'.$watchedTime.'" data-show="'.$showID.'" id="time"></span>';
    }

    $conn->close();
}
?>