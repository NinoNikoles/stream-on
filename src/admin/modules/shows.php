<?php
//-- Saves show with informations in database -- 
function insertShow($showID) {
    $conn = dbConnect();
    $tmdb = setupTMDB();

    $conn->begin_transaction();

    $id = $showID;
    $show = $tmdb->getTVShow($id);
    $title =  $show->getName();
    $overview = $show->getOverview();
    $poster = $show->getPoster();
    $backdrop = $show->getBackdrop();
    $rating = $show->getVoteAverage();
    $release = $show->getReleaseDate();
    $seasons = $show->getSeasons();
    $seasonsCount = $show->getNumSeasons();
    $episodesCount = $show->getNumEpisodes();
    $genres = $show->getGenres();

    $data = [];
    $dataSeasonsIDs = [];

    foreach ( $genres as $genre ) {
        $data[] = $genre->getID();
    }
    
    foreach ( $seasons as $season ) {
        $dataSeasonsIDs[] = $season->getID();
    }
    
    $seasonsIDs = json_encode($dataSeasonsIDs);
    $genresString = json_encode($data);

    try {
        // Füge den neuen Film in die "shows"-Tabelle ein
        $showQuery = "INSERT INTO shows (
                show_tmdbID,
                show_title,
                show_overview,
                show_poster,
                show_thumbnail,
                show_rating,
                show_release,
                show_season_count,
                show_seasons,
                show_episodes_count,
                show_genres
            ) VALUES (
                '$id',
                '$title',
                '$overview',
                '$poster',
                '$backdrop',
                '$rating',
                '$release',
                '$seasonsCount',
                '$seasonsIDs',
                '$episodesCount',
                '$genresString'
        )";

        $conn->query($showQuery);

        foreach($genres as $genre) {
            $genreID = $genre->getId();
    
            // Füge die Genre-Verbindung in die "show_genre"-Tabelle ein
            $genreQuery = "INSERT INTO show_genre (show_id, genre_id) VALUES ($id, $genreID)";
            $conn->query($genreQuery);
        }

        $seasonsData = [];

        foreach ( $seasons as $season ) {
            $title = str_replace("'", '"', $season->getName());
            $overview = str_replace("'", '"', $season->getOverview());
            $seasonsData[] = "('".$season->getID()."','".$title."','".$overview."','".$season->getPoster()."','".$season->getSeasonNumber()."','".$season->getVoteAverage()."','".$season->getAirDate()."','".$season->getEpisodeCount()."','".$id."')";
        }

        $dataSring = json_encode($seasonsData, JSON_UNESCAPED_UNICODE);
        $dataSring = str_replace(array('[', ']', "[", "]"), '', $dataSring);
        $dataSring = str_replace('"(', '(', $dataSring);
        $dataSring = str_replace(')"', ')', $dataSring);
        $dataSring = str_replace(",',", ", NULL,", $dataSring);
        $dataSring = stripslashes($dataSring);

        $sqlSeasons = "INSERT INTO seasons (season_tmdbID, season_title, season_overview, season_poster, season_number, season_rating, season_release, season_episodes_count, season_show_id) VALUES $dataSring";
        $conn->query($sqlSeasons);
        
        // Commit der Transaktion
        $conn->commit();
        $conn->close();
        set_callout('success','add_show_success');
        //page_redirect("/admin/shows");
    } catch (Exception $e) {
        // Bei einem Fehler Rollback der Transaktion
        $conn->rollback();
        set_callout('alert','add_show_alert');
        //page_redirect("/admin/shows");
    }
}

function selectAllShowsByTitle($order = '') {
    $conn = dbConnect();
    
    if ( $order != '' ) {
        $sql = "SELECT show_tmdbID, show_title, show_overview, show_poster, show_thumbnail, show_rating, show_release, show_season_count, show_genres FROM shows ORDER BY show_title $order";
    } else {
        $sql = "SELECT show_tmdbID, show_title, show_overview, show_poster, show_thumbnail, show_rating, show_release, show_season_count, show_genres FROM shows";
    }
    
    $results = $conn->query($sql);

    if ( $results->num_rows > 0 ) {
            
        $shows = [];
        while ($rows = $results->fetch_assoc() ) {
            $shows[] = $rows;
        }
    }

    $conn->close();
    return $shows;
}

function show_card($show, $extraClasses = '') {
    $conn = dbConnect();

    $showID = $show['show_tmdbID'];
    $title = $show['show_title'];
    $overview = $show['show_overview'];
    $rating = $show['show_rating'];
    $showRelease = new DateTime($show['show_release']);
    $releaseYear = $showRelease->format('Y');
    $poster = $show['show_poster'];
    $backdrop = $show['show_thumbnail'];
    $genres = json_decode($show['show_genres']);
    $genreHTML = '';
    foreach ( $genres as $genre ) {
        $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
    }

    $userID = intval($_SESSION['userID']);
    var_dump($show['show_season_count']);
    if ( intval($show['show_season_count']) === 1 ) {
        $seasonOutput = '1 '.lang_snippet('season');
    } else {
        $seasonOutput = $show['show_season_count'].' '.lang_snippet('seasons');
    }

    /*$watchingSQL = "SELECT watched_seconds, total_length FROM show_watched WHERE user_id = $userID and show_id = $showID and watched_seconds > 0";
    $watchInfos = $conn->query($watchingSQL);
    if ( $watchInfos->num_rows > 0 ) {
        while ( $watchInfo = $watchInfos->fetch_assoc() ) {
            $watchedInPercent = getWatchedTime($watchInfo['watched_seconds'], $watchInfo['total_length']);
        }
        $timebar = '<div class="watched-bar"><progress max="100" value="'.$watchedInPercent.'"></progress></div>';
    } else {
        $timebar = '';
    }*/

    /*$watchListCheckSQL = "SELECT id FROM watchlist WHERE user_id=$userID and show_id=$showID";

    if ( $conn->query($watchListCheckSQL)->num_rows > 0 ) {
        $listButtons = '
        <a href="#" class="btn btn-small btn-white icon-left icon-add mylist-btn add-to-list hidden loading" data-show-id="'.$showID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-small btn-white icon-left icon-remove mylist-btn remove-from-list loading" data-show-id="'.$showID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    } else {
        $listButtons = '
        <a href="#" class="btn btn-small btn-white icon-left icon-add mylist-btn add-to-list loading" data-show-id="'.$showID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-small btn-white icon-left icon-remove mylist-btn remove-from-list hidden loading" data-show-id="'.$showID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    }*/

    if ( $_SESSION['role'] === "1" ) {
        $editBtn = '<a href="/admin/show/?id='.$showID.'" title="'.lang_snippet('edit').'" class="edit-trigger"></a>';
    }

    $card = '
        <div class="'.$extraClasses.'">
            <div class="media-card">
                <div class="media-card-wrapper">
                    <figure class="widescreen desktop-only">
                        <img src="'.loadImg('original', $backdrop).'" loading="lazy" importance="low">
                    </figure>
                    <figure class="poster mobile-only">
                        <img src="'.loadImg('original', $poster).'" loading="lazy" importance="low">
                    </figure>
                    <div class="link-wrapper">
                        <a href="/watch/?id='.$showID.'" title="'.$title.'" class="play-trigger"></a>
                        <a href="#content-'.$showID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$showID.'"></a>
                        '.$editBtn.'
                    </div>
                </div>


                <div class="info-popup" id="content-'.$showID.'" style="display:none;">
                    <div class="col12 marg-bottom-xs mobile-only">
                        <figure class="widescreen">
                            <img src="'.loadImg('original', $backdrop).'" loading="lazy" importance="low">
                        </figure>
                    </div>
                    <div class="innerWrap">
                        <div class="col7 marg-right-col1">
                            <p class="h2">'.$title.'</p>
                            <p class="small tag-list marg-bottom-base">
                                <span class="tag">'.$releaseYear.'</span>
                                <span class="tag">'.$rating.'/10 ★</span>
                                <span class="tag">'.$seasonOutput.'</span>
                            </p>
                            <a href="/watch/?id='.$showID.'" class="btn btn-small btn-white icon-left icon-play marg-right-xs">Jetzt schauen</a>
                            <p class="small">'.$overview.'</p>
                            <p class="small tag-list">'.$genreHTML.'</p>
                            '.getTrailer($showID, 'marg-top-xs marg-bottom-xs').'
                        </div>
                        <div class="col4 desktop-only">
                            <figure class="poster">
                                <img src="'.loadImg('original', $poster).'" alt="" loading="lazy" importance="low">
                            </figure>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
    $conn->close();
    return $card;
}