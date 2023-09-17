<?php

$mediaID = $_POST['mediaID'];
$conn = dbConnect();

$sql = "SELECT * FROM media WHERE tmdbID=$mediaID";
$result = $conn->query($sql);

while ( $media = $result->fetch_assoc() ) {

    $userID = intval($_SESSION['userID']);
    $mediaID = $media['tmdbID'];
    $title = $media['title'];
    $overview = $media['overview'];
    $rating = $media['rating'];
    $mediaRelease = new DateTime($media['releaseDate']);
    $releaseYear = $mediaRelease->format('Y');
    $poster = $media['poster'];
    $backdrop = $media['backdrop'];
    $type = $media['mediaType'];
    $select = '';
    $options = '';
    $seasonWrap = '';
    $extraSeasonWrap = '';
    $extras = '';

    ////-- Loads seasons and episodes of seasons --////
    if ( $type === 'movie' ) {
        //$tagline = $media['tagline'];
        $extraInfo = runtimeToString($media['runtime']);
    } else {
        if ( intval($media['show_season_count']) === 1 ) {
            $extraInfo = '1 '.lang_snippet('season');
        } else {
            $extraInfo = $media['show_season_count'].' '.lang_snippet('seasons');
        }
    
        // Fetch seasons
        $querySeasons = "SELECT tmdbID, title, season_number, episodes_count FROM seasons WHERE show_tmdbID=$mediaID";
        $seasonResults = $conn->query($querySeasons);        

        // Go through all seasons
        if ( $seasonResults->num_rows > 0 ) {
            while ( $seasonRow = $seasonResults->fetch_assoc() ) {

                // fetch all episodes of season
                $fetchEpisodes = "SELECT * FROM episodes WHERE show_id=$mediaID and season_number = ".$seasonRow['season_number']." ORDER BY episode_number ASC";
                $episodesResult = $conn->query($fetchEpisodes);

                $episodeList = '';

                // Go through all episodes of season
                if ( $episodesResult->num_rows > 0 ) {
                    while ( $episodeRow = $episodesResult->fetch_assoc() ) {
                        if ( $seasonRow['season_number'] === $episodeRow['season_number']) {
                            $episodeID = $episodeRow['tmdbID'];
                            $episodeBackdrop = $episodeRow['backdrop'];
                            $episodeOverview = $episodeRow['overview'];
                            $episodeNumberRun = $episodeRow['episode_number'];
                            $episodeTitleRun = $episodeRow['title'];
                            $episodeWatchTrigger = '';
                            $episodeDisabled = 'disabled';
                            $watchedClass = '';

                            $watchCheck = "SELECT * FROM media_watched WHERE user_id = ".$_SESSION['userID']." AND media_id = $episodeID;";
                            $watchCheckResult = $conn->query($watchCheck);
                            
                            if ( $watchCheckResult->num_rows > 0 ) {
                                while ( $watchInfoChecked = $watchCheckResult->fetch_assoc() ) {
                                    $watchedClass = 'watched-'.round(getWatchedTime($watchInfoChecked['watched_seconds'], $watchInfoChecked['total_length']), 0);
                                }
                            }

                            if ( $episodeRow['file_path'] != "" ) {
                                $episodeWatchTrigger = '<div class="link-wrapper">
                                    <a href="/watch/?s='.$mediaID.'&id='.$episodeID.'" title="'.$title.'" class="play-trigger">
                                        <span class="icon-wrap col-5 col-3-medium pad-top-xs pad-bottom-xs">
                                            <i class="icon-play"></i>
                                        </span>
                                    </a>
                                    </div>';
                                $episodeDisabled = '';
                            }
    
                            // creates eipsode item for show
                            $episodeList.= '<div class="col12 media-card-episode '.$episodeDisabled.' '.$watchedClass.' pad-top-xs pad-bottom-xs">
                                <div class="col-5 col-3-medium">
                                    <figure class="widescreen">
                                        <img data-img="'.loadImg('original', $episodeBackdrop).'" alt="'.$title.'">
                                    </figure>
                                </div>
                                <div class="col-7 col-9-medium pad-left-xs">
                                    <p class="small strong marg-no">'.lang_snippet('episode').' '.$episodeNumberRun.': '.truncate($episodeTitleRun, 50).'</p>
                                    <p class="small">'.truncate($episodeOverview, 100).'</p>
                                </div>
                                '.$episodeWatchTrigger.'
                            </div>';
                        }                        
                    }
                }
                
                // Generate season select
                // since season 0 is always extras, it will be added at the end
                if ( $seasonRow['season_number'] === '0' ) {
                    $extras = '<option value="'.$seasonRow['season_number'].'">'.$seasonRow['title'].'</option>';
                    $extraSeasonWrap .= '<div class="col12 select-tab-content season-select-'.$mediaID.'" data-select-tab="'.$seasonRow['season_number'].'">'.$episodeList.'</div>';
                } else if ( $seasonRow['season_number'] === '1' ) {
                    $options .= '<option value="'.$seasonRow['season_number'].'">'.$seasonRow['title'].'</option>';
                    $seasonWrap .= '<div class="col12 select-tab-content season-select-'.$mediaID.' is-active" data-select-tab="'.$seasonRow['season_number'].'">'.$episodeList.'</div>';
                } else {
                    $options .= '<option value="'.$seasonRow['season_number'].'">'.$seasonRow['title'].'</option>';
                    $seasonWrap .= '<div class="col12 select-tab-content season-select-'.$mediaID.'" data-select-tab="'.$seasonRow['season_number'].'">'.$episodeList.'</div>';
                }
            }

            // Season select
            $select = '<p><label>'.lang_snippet('seasons').' <select class="tab-select season-select-'.$mediaID.'" id="season-select-'.$mediaID.'">'.$options.$extras.'</select></label></p>';
            // episodes lists for seasons
            $seasonWrap = $seasonWrap.$extraSeasonWrap;
        }
    }
    
    ////-- Generates genre tag list --////
    $genres = json_decode($media['genres']);
    $genreHTML = '';
    foreach ( $genres as $genre ) {
        $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
    }

    ////-- Watch list --////
    $watchListCheckSQL = "SELECT id FROM watchlist WHERE user_id=$userID and media_id=$mediaID";

    // Adds watchlist buttons
    if ( $conn->query($watchListCheckSQL)->num_rows > 0 ) {
        $listButtons = '
        <a href="#" class="btn btn-small btn-white icon-left icon-add mylist-btn add-to-list hidden loading" data-media-id="'.$mediaID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-small btn-white icon-left icon-remove mylist-btn remove-from-list loading" data-media-id="'.$mediaID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    } else {
        $listButtons = '
        <a href="#" class="btn btn-small btn-white icon-left icon-add mylist-btn add-to-list loading" data-media-id="'.$mediaID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-small btn-white icon-left icon-remove mylist-btn remove-from-list hidden loading" data-media-id="'.$mediaID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    }

    ////-- Adds edit btn when user is admin --////
    if ( $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin' ) {
        if ( $type === 'movie' ) {
            $editBtn = '<a href="/admin/movie/?id='.$mediaID.'" title="'.lang_snippet('edit').'" class="edit-trigger"></a>';
        } else if ( $type === 'show' ) {
            $editBtn = '<a href="/admin/show/?id='.$mediaID.'" title="'.lang_snippet('edit').'" class="edit-trigger"></a>';
        }
    }

    ////-- Play buttons and time progressbar --////
    $watchTrigger= '';
    $watchBtn = '';
    $timebar = '';
    $disabled = '';
    
    if ( $type === 'movie' ) {
        $disabled = 'disabled';
        
        // Add play btn when media has file path
        if ( $media['file_path'] != "" ) {
            $watchTrigger = '<a href="/watch/?id='.$mediaID.'" title="'.$title.'" class="play-trigger"></a>';
            $watchBtn = '<a href="/watch/?id='.$mediaID.'" class="btn btn-small btn-white icon-left icon-play marg-right-xs">'.lang_snippet('watch_now').'</a>';
            $disabled = '';
        }

        // Adds watch progress bar movie
        $watchingSQL = "SELECT watched_seconds, total_length FROM media_watched WHERE user_id = $userID and media_id = $mediaID and watched_seconds > 0";
        $watchInfos = $conn->query($watchingSQL);
        if ( $watchInfos->num_rows > 0 ) {
            while ( $watchInfo = $watchInfos->fetch_assoc() ) {
                $watchedInPercent = getWatchedTime($watchInfo['watched_seconds'], $watchInfo['total_length']);
            }
            $timebar = '<div class="watched-bar"><progress max="100" value="'.$watchedInPercent.'"></progress></div>';
        }
    } else {
        // Adds watch progress bar show
        $currEpisodeSQL = "SELECT * FROM media_watched WHERE user_id = $userID and show_id = $mediaID AND watched_seconds > 0 ORDER BY last_watched LIMIT 1";
        $currEpisodeResult = $conn->query($currEpisodeSQL);

        // Wenn eine Episode geschaut wurde
        if ( $currEpisodeResult->num_rows > 0 ) {
            while ( $currEpisode = $currEpisodeResult->fetch_assoc() ) {
                $episodeID = $currEpisode['media_id'];
                $currEpisodeTime = getWatchedTime($currEpisode['watched_seconds'], $currEpisode['total_length']);
            }

            // Sets time bar for last episode watched
            $timebar = '<div class="watched-bar"><progress max="100" value="'.$currEpisodeTime.'"></progress></div>';

            $watchTrigger = '<a href="/watch/?s='.$mediaID.'&id='.$episodeID.'" title="'.$title.'" class="play-trigger"></a>';
            $watchBtn = '<a href="/watch/?s='.$mediaID.'&id='.$episodeID.'" class="btn btn-small btn-white icon-left icon-play marg-right-xs">'.lang_snippet('watch_now').'</a>';
        } else {
            // Adds watch progress bar show
            $firstEpisodeSQL = "SELECT e.tmdbID AS episode_tmdbID, e.file_path, s.tmdbID AS season_tmdb 
            FROM episodes e
            INNER JOIN seasons s ON e.show_id = s.show_tmdbID AND e.season_number = s.season_number
            WHERE e.show_id = $mediaID 
            AND e.season_number = 1 
            AND e.episode_number = 1 
            AND s.season_number = 1";
            $firstEpisodeResult = $conn->query($firstEpisodeSQL);

            if ( $firstEpisodeResult->num_rows > 0 ) {
                while ( $firstEpisode = $firstEpisodeResult->fetch_assoc() ) {
                    if ( $firstEpisode['file_path'] != NULL ) {
                        // Sets play button for first episode of show
                        $watchTrigger = '<a href="/watch/?s='.$mediaID.'&id='.$firstEpisode['episode_tmdbID'].'" title="'.$title.'" class="play-trigger"></a>';
                        $watchBtn = '<a href="/watch/?s='.$mediaID.'&id='.$firstEpisode['episode_tmdbID'].'" class="btn btn-small btn-white icon-left icon-play marg-right-xs">'.lang_snippet('watch_now').'</a>';
                    } else {
                        $disabled = 'disabled';
                    }
                }
            } else {
                $disabled = 'disabled';
            }
        }
    }
}

    $popup =    '<div class="info-popup" id="'.$mediaID.'" style="display:none;">
                    <div class="col12 marg-bottom-xs mobile-only">
                        <figure class="widescreen">
                            <img data-img="'.loadImg('original', $backdrop).'" loading="lazy" importance="low" alt="'.$title.'">
                        </figure>
                    </div>
                    <div class="innerWrap">
                        <div class="col7 marg-right-col1">
                            <p class="h2">'.$title.'</p>
                            <p class="small tag-list marg-bottom-base">
                                <span class="tag">'.$releaseYear.'</span>
                                <span class="tag">'.$rating.'/10 ★</span>
                                <span class="tag">'.$extraInfo.'</span>
                            </p>
                            '.$watchBtn.$listButtons.'
                            <p class="small">'.$overview.'</p>
                            <p class="small tag-list marg-bottom-base">'.$genreHTML.'</p>
                            '.$select.$seasonWrap.'
                            '.getTrailer($mediaID, 'marg-top-xs marg-bottom-xs').'
                        </div>
                        <div class="col4 desktop-only">
                            <figure class="poster">
                                <img data-img="'.loadImg('original', $poster).'" alt="" loading="lazy" importance="low" alt="'.$title.'">
                            </figure>
                        </div>
                    </div>
                </div>';

    echo $popup;
?>