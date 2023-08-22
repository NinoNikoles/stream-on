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
        echo '<span data-time="'.$watchedTime.'" data-show="'.$movieID.'" id="time"></span>';
    }

    $conn->close();
}

function showVideoPlayer($episodeID, $showID, $fullscreen = false) {
    $conn = dbConnect();
    $sql = "SELECT id, tmdbID, episode_number, file_path, overview, backdrop FROM episodes WHERE tmdbID=$episodeID AND show_id=$showID;";

    $episodeResult = $conn->query($sql);
    if ( $episodeResult->num_rows > 0 ) {
        while ( $episode = $episodeResult->fetch_assoc() ) {
            $filePath = $episode['file_path'];
            $episodeNumber = $episode['episode_number'];
            $id = $episode['id'];
            $info = $episode['overview'];
            $backdrop = $episode['backdrop'];
        }
    }



    $nextEpisodeID = null;

    // Nun die nächstmögliche id mit derselben show_id und größerer episode_number finden
    $sql = "SELECT id, tmdbID, episode_number, file_path, backdrop FROM episodes WHERE show_id = $showID AND id > $id AND episode_number > $episodeNumber ORDER BY episode_number ASC, id ASC LIMIT 1;";
    $nextResult = $conn->query($sql);

    if ($nextResult->num_rows > 0) {
        while ( $nextEpisode = $nextResult->fetch_assoc() ) {
            $nextTMDBID = $nextEpisode['tmdbID'];
            $nextFilePath = $nextEpisode['file_path'];
            $nextBackdrop = $nextEpisode['backdrop'];

            if ( !($nextFilePath == NULL) || !($nextFilePath == '') ) {
                $nextBTN = '<a href="/watch/?s='.$showID.'&id='.$nextTMDBID.'" id="next-episode-btn" class="next-episode-btn">
                    <figure class="widescreen"><img src="'.loadImg('original', $nextBackdrop).'"><i class="icon icon-play"></i></figure>
                    <span>'.lang_snippet('next_episode').'</span>
                </a>';
            } else {
                $nextBTN = '';  
            } 
        }
    } else {
        $nextBTN = '';
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
                echo $nextBTN;
            echo '</figure>';
           
            
            echo '<script>
                const videoSRC = document.getElementById("player");
                const nextEpisodeBtn = document.getElementById("next-episode-btn");

                if (nextEpisodeBtn){
                    videoSRC.addEventListener("timeupdate", function() {
                        const currentTime = videoSRC.currentTime;
                        const duration = video.duration;
                        const last20Seconds = duration - 20;

                        if (currentTime >= last20Seconds) {
                            nextEpisodeBtn.classList.add("visible");
                        }

                        if (currentTime <= last20Seconds && nextEpisodeBtn.classList.contains("visible") ) {
                            nextEpisodeBtn.classList.remove("visible");
                        }
                    });
                }
            </script>';
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