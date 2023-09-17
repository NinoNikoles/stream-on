<?php
function movieVideoPlayer($movieID, $fullscreen = false, $session = false) {
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
                echo '<a href="/" id="player-back-btn" title="Back"></a>';
                if(!$session) {
                    echo '<a href="/watchtogether/?id='.$movieID.'&uuid='.getUUID().'" id="player-session-btn" title="Start group session"></a>';
                } else {
                    echo '<a href="#" id="chat-open"></a>';
                }
                echo '<a href="#" id="player-sek-forward" class="icon icon-skip-time" title="Skip 10 Sek"></a>';
                echo '<a href="#" id="player-sek-back" class="icon icon-time-back" title="Go 10 Sek back"></a>';
            echo '</figure>';
            //
        } else {
            echo '<figure class="widescreen">';
                echo '<video id="player" class="video-js" data-id="'.$movieID.'" data-sound="true" data-fullscreen="true" controls preload="auto" poster="'.loadImg('original', $backdrop).'">'; //'.$tmdb->getImageURL().$backdrop.'
                    echo '<source src="'.$filePath.'" type="video/mp4" />';
                echo '</video>';
            echo '</figure>';
        }

        if ( isset($_GET['uuid']) ) {
            $sessionData = 'data-session="'.$_GET['uuid'].'"';
        } else {
            $sessionData = "";
        }

        $volume = getVolume($userID);
        echo '<span data-time="'.$watchedTime.'" data-show="'.$movieID.'" '.$sessionData.' data-volume="'.$volume.'" id="time"></span>';

        if ( isset($_GET['uuid']) ) {
            require_once ROOT_PATH.'/views/includes/chat.php';
        }
    }

    $conn->close();
}

function showVideoPlayer($episodeID, $showID, $fullscreen = false, $session = false) {
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

    $nextTMDBID = null;

    // Nun die nächstmögliche id mit derselben show_id und größerer episode_number finden
    $sql = "SELECT id, tmdbID, episode_number, file_path, backdrop FROM episodes WHERE show_id = $showID AND id > $id AND episode_number > $episodeNumber ORDER BY episode_number ASC, id ASC LIMIT 1;";
    $nextResult = $conn->query($sql);

    if ($nextResult->num_rows > 0) {
        while ( $nextEpisode = $nextResult->fetch_assoc() ) {
            $nextTMDBID = $nextEpisode['tmdbID'];
            $nextFilePath = $nextEpisode['file_path'];
            $nextBackdrop = $nextEpisode['backdrop'];            
        }

        $timeSelect = "SELECT watched_seconds, total_length FROM media_watched WHERE media_id=".$nextTMDBID." AND user_id=".$_SESSION['userID'].";";
        $timeResult = $conn->query($timeSelect);

        if ($timeResult->num_rows > 0) {
            while ( $timeEpisode = $timeResult->fetch_assoc() ) {
                $nextTimeWatched = $timeEpisode['watched_seconds'];
                $nextTotalTime = $timeEpisode['total_length'];
            }
        } else {
            $nextTimeWatched = 0;
            $nextTotalTime = 10000;
        }

        if ( !($nextFilePath == NULL) || !($nextFilePath == '') ) {
            if ( $session ) {
                $nextBTN = '<a href="/watchtogether/?s='.$showID.'&id='.$nextTMDBID.'&uuid='.$_GET['uuid'].'" id="next-episode-btn" class="next-episode-btn">
                    <figure class="widescreen"><img src="'.loadImg('original', $nextBackdrop).'"><i class="icon icon-play"></i></figure>
                    <span>'.lang_snippet('next_episode').'</span>
                </a>';
            } else {
                $nextBTN = '<a href="/watch/?s='.$showID.'&id='.$nextTMDBID.'" id="next-episode-btn" class="next-episode-btn" data-id="'.$nextTMDBID.'" data-current-time="'.$nextTimeWatched.'" data-length="'.$nextTotalTime.'">
                    <figure class="widescreen"><img src="'.loadImg('original', $nextBackdrop).'"><i class="icon icon-play"></i></figure>
                    <span>'.lang_snippet('next_episode').'</span>
                </a>';
            }                
        } else {
            $nextBTN = '';  
        } 
    } else {
        $nextBTN = '';
    }

    // Generating episodes window
    $sql = "SELECT id, tmdbID, title, season_number, episodes_count FROM seasons WHERE show_tmdbID=$showID;";
    $seasonsResult = $conn->query($sql);

    $seasonList = '';
    $options = '';
    $seasonWrap = '';
    $extraSeasonWrap = '';
    $extras = '';
    $subMenuList = "";

    if ($seasonsResult->num_rows > 0) {
        while ( $seasonRow = $seasonsResult->fetch_assoc() ) {

            // fetch all episodes of season
            $fetchEpisodes = "SELECT tmdbID, title, episode_number, overview, backdrop, season_number, file_path FROM episodes WHERE show_id=$showID and season_number = ".$seasonRow['season_number']." ORDER BY episode_number ASC";
            $episodesResult = $conn->query($fetchEpisodes);

            $episodeList = '';

            // Go through all episodes of season
            if ( $episodesResult->num_rows > 0 ) {
                while ( $episodeRow = $episodesResult->fetch_assoc() ) {
                    if ( $seasonRow['season_number'] === $episodeRow['season_number']) {
                        $episodeIDrun = $episodeRow['tmdbID'];
                        $episodeBackdropRun = $episodeRow['backdrop'];
                        $episodeTitleRun = $episodeRow['title'];
                        $episodeNumberRun = $episodeRow['episode_number'];
                        $episodeOverviewRun = $episodeRow['overview'];
                        $episodeWatchTrigger = '';
                        $episodeDisabled = 'disabled';
                        $watchedClass = "";

                        $watchCheck = "SELECT * FROM media_watched WHERE user_id = ".$_SESSION['userID']." AND media_id = $episodeIDrun;";
                        $watchCheckResult = $conn->query($watchCheck);
                        
                        if ( $watchCheckResult->num_rows > 0 ) {
                            while ( $watchInfoChecked = $watchCheckResult->fetch_assoc() ) {
                                $watchedClass = 'watched-'.round(getWatchedTime($watchInfoChecked['watched_seconds'], $watchInfoChecked['total_length']), 0);
                            }
                        }
                        

                        if ( $episodeRow['file_path'] != "" ) {
                            if($session) {
                                $episodeWatchTrigger = '<div class="link-wrapper">
                                    <a href="/watchtogether/?s='.$showID.'&id='.$episodeIDrun.'&uuid='.$_GET['uuid'].'" class="play-trigger" title="'.lang_snippet('episode').' '.$episodeNumberRun.': '.$episodeTitleRun.'">
                                            <span class="icon-wrap col-3 pad-top-xs pad-bottom-xs">
                                            <i class="icon-play"></i>
                                        </span>
                                    </a>
                                    </div>';
                            } else {
                                $episodeWatchTrigger = '<div class="link-wrapper">
                                    <a href="/watch/?s='.$showID.'&id='.$episodeIDrun.'" class="play-trigger" title="'.lang_snippet('episode').' '.$episodeNumberRun.': '.$episodeTitleRun.'">
                                        <span class="icon-wrap col-3 pad-top-xs pad-bottom-xs">
                                            <i class="icon-play"></i>
                                        </span>
                                    </a>
                                    </div>';
                            }
                            $episodeDisabled = '';
                        }

                        // creates eipsode item for show
                        $episodeList.= '<li class="list-item pad-left-xs pad-right-xs">
                        <div class="col12 media-card-episode '.$episodeDisabled.' '.$watchedClass.' pad-top-xs pad-bottom-xs">
                            <div class="col-3">
                                <figure class="widescreen">
                                    <img data-img="'.loadImg('original', $episodeBackdropRun).'">
                                </figure>
                            </div>
                            <div class="col-9 pad-left-xs">
                                <p class="small strong marg-no">'.lang_snippet('episode').' '.$episodeNumberRun.': '.truncate($episodeTitleRun, 50).'</p>
                                <p class="smaller">'.truncate($episodeOverviewRun, 100).'</p>
                            </div>
                            '.$episodeWatchTrigger.'
                        </div></li>';
                    }                        
                }
            }
            
            // Generate season select
            // since season 0 is always extras, it will be added at the end
            if ( $seasonRow['season_number'] === '0' ) {
                $extraSeasonWrap .= '<li class="list-item"><a href="#season-'.$seasonRow['tmdbID'].'-container" data-id="'.$seasonRow['tmdbID'].'">'.$seasonRow['title'].'<span class="icon-right icon-chevron-right">'.$seasonRow['episodes_count'].' '.lang_snippet('episodes').'</span></a></li>';
            } else if ( $seasonRow['season_number'] === '1' ) {
                $seasonWrap .= '<li class="list-item"><a href="#season-'.$seasonRow['tmdbID'].'-container" data-id="'.$seasonRow['tmdbID'].'">'.$seasonRow['title'].'<span class="icon-right icon-chevron-right">'.$seasonRow['episodes_count'].' '.lang_snippet('episodes').'</span></a></li>';
            } else {
                $seasonWrap .= '<li class="list-item"><a href="#season-'.$seasonRow['tmdbID'].'-container" data-id="'.$seasonRow['tmdbID'].'">'.$seasonRow['title'].'<span class="icon-right icon-chevron-right">'.$seasonRow['episodes_count'].' '.lang_snippet('episodes').'</span></a></li>';
            }

            $subMenuList .= '<ul class="sub-menu" id="'.$seasonRow['tmdbID'].'"><a href="#" class="back icon-left icon-chevron-left">'.lang_snippet("seasons").'</a>'.$episodeList.'</ul>';
            
        }
    }

    $seasonList = $seasonWrap.$extraSeasonWrap;
    $showContainer = '<div id="show-container"><ul class="menu">'.$seasonList.'</ul>'.$subMenuList.'</div>';

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
                echo '<a href="/" id="player-back-btn" title="Back"></a>';
                if(!$session) {
                    echo '<a href="/watchtogether/?s='.$showID.'&id='.$episodeID.'&uuid='.getUUID().'" id="player-session-btn" title="Start group session"></a>';
                } else {
                    echo '<a href="#" id="chat-open"></a>';
                }
                echo '<a href="#" id="player-sek-forward" class="icon icon-skip-time" title="Skip 10 Sek"></a>';
                echo '<a href="#" id="player-sek-back" class="icon icon-time-back" title="Go 10 Sek back"></a>';
                echo '<a href="#" id="show-eps-btn" class="icon icon-multilayer" title="All episodes"></a>';
                echo $nextBTN;
                echo $showContainer;
            echo '</figure>';
           
            
            echo '<script>
                
            
            </script>';
        } else {
            echo '<figure class="widescreen">';
                echo '<video id="player" class="video-js" data-id="'.$episodeID.'" data-sound="true" data-fullscreen="true" controls preload="auto" poster="'.loadImg('original', $backdrop).'">'; //'.$tmdb->getImageURL().$backdrop.'
                    echo '<source src="'.$filePath.'" type="video/mp4" />';
                echo '</video>';
            echo '</figure>';
        }

        if ( isset($_GET['uuid']) ) {
            $sessionData = 'data-session="'.$_GET['uuid'].'"';
        } else {
            $sessionData = "";
        }

        $volume = getVolume($userID);
        echo '<span data-time="'.$watchedTime.'" data-show="'.$showID.'" '.$sessionData.' data-volume="'.$volume.'" id="time"></span>';
    
        if ( isset($_GET['uuid']) ) {
            require_once ROOT_PATH.'/views/includes/chat.php';
        }
    }

    $conn->close();
}
?>