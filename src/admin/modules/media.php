<?php
////////// GENERAL

//-- Check if media is in local database --
function mediaInLocalDB($mediaID) {
    $conn = dbConnect();

    $sql = "SELECT tmdbID FROM media WHERE tmdbID='$mediaID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $conn->close();
        return true;
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////// MOVIES

//-- Saves movie with informations in database -- 
function insertMovie($movieID) {
    $conn = dbConnect();
    $tmdb = setupTMDB();

    $conn->begin_transaction();

    $id = mysqli_real_escape_string($conn, $movieID);
    $movie = $tmdb->getMovie($id);
    $title =  mysqli_real_escape_string($conn, $movie->getTitle());
    $tagline =  mysqli_real_escape_string($conn, $movie->getTagline());
    $overview = mysqli_real_escape_string($conn, $movie->getOverview());
    $poster = mysqli_real_escape_string($conn, $movie->getPoster());
    $backdrop = mysqli_real_escape_string($conn, $movie->getBackdrop());
    $rating = mysqli_real_escape_string($conn, $movie->getVoteAverage());
    $release = mysqli_real_escape_string($conn, $movie->getReleaseDate());
    $runtime = mysqli_real_escape_string($conn, $movie->getRuntime());
    $collection = intval(mysqli_real_escape_string($conn, $movie->getCollection()));
    $genres = $movie->getGenres();

    $data = [];

    foreach ( $genres as $genre ) {
        $data[] = $genre->getID();
    }

    $genresString = json_encode($data);

    try {
        // Füge den neuen Film in die "movies"-Tabelle ein
        $movieQuery = "INSERT INTO media (
                tmdbID,
                title,
                tagline,
                overview,
                poster,
                backdrop,
                rating,
                releaseDate,
                runtime,
                movieCollection,
                genres,
                mediaType
            ) VALUES (
                '$id',
                '$title',
                '$tagline',
                '$overview',
                '$poster',
                '$backdrop',
                '$rating',
                '$release',
                '$runtime',
                '$collection',
                '$genresString',
                'movie'
        )";

        $conn->query($movieQuery);

        foreach($genres as $genre) {
            $genreID = $genre->getId();
            $mediaID = intval($id);
            // Füge die Genre-Verbindung in die "media_genre"-Tabelle ein
            $genreQuery = "INSERT INTO media_genre (media_id, genre_id) VALUES ($mediaID, $genreID)";
            $conn->query($genreQuery);
        }

        // Commit der Transaktion
        $conn->commit();
        $conn->close();
        set_callout('success','add_movie_success');
        page_redirect("/admin/movie/?id=$id");
    } catch (Exception $e) {
        // Bei einem Fehler Rollback der Transaktion
        $conn->rollback();
        set_callout('alert','add_movie_alert');
        page_redirect("/admin/movies");
    }

    $conn->close();
}

// Delete Movie
function deleteMovie($movieID) {
    $conn = dbConnect();

    $genreDeleteQuery = "DELETE FROM media_genre WHERE media_id = $movieID";
    $conn->query($genreDeleteQuery);
    if (!($conn->query($genreDeleteQuery) === TRUE)) {
        $conn->close();
        set_callout('alert','delete_movie_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    }

    $highlightDeleteQuery = "DELETE FROM highlights WHERE media_id = $movieID";
    $conn->query($highlightDeleteQuery);
    if (!($conn->query($highlightDeleteQuery) === TRUE)) {
        $conn->close();
        set_callout('alert','delete_movie_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    }

    // Lösche den Film aus der movies-Tabelle
    $filmDeleteQuery = "DELETE FROM media WHERE id = $movieID";
    $conn->query($filmDeleteQuery);
    if (!($conn->query($filmDeleteQuery) === TRUE)) {
        $conn->close();
        set_callout('alert','delete_movie_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    }

    $conn->close();
    set_callout('success','delete_movie_success');
    page_redirect("/admin/movies");
}

//-- Returns all information of a movie from local database --
function selectMovieByID($movieID) {
    $tmdb = setupTMDB();
    $conn = dbConnect();

    $sql = "SELECT
    tmdbID,
    title,
    tagline,
    overview,
    poster,
    backdrop,
    rating,
    releaseDate,
    runtime,
    movieCollection,
    file_path,
    genres,
    trailer,
    mediaType
    FROM media
    WHERE tmdbID=$movieID";
    $result = $conn->query($sql);

    $data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data['tmdbID'] = $row['tmdbID'];
            $data['title'] = $row['title'];
            $data['backdrop'] = $row['backdrop'];
            $data['poster'] = $row['poster'];
            if ( !is_null($row['tagline']) ) {
                $data['tagline'] = $row['tagline'];
            } else {
                $data['tagline'] = '';
            }            
            $data['overview'] = $row['overview'];
            $data['rating'] = $row['rating'];
            $data['release'] = $row['releaseDate'];
            $data['runtime'] = intval($row['runtime']);
            if ( !is_null($row['movieCollection']) ) {
                $data['collection'] = intval($row['movieCollection']);
            } else {
                $data['collection'] = '';
            }
            $data['trailer'] = $row['trailer'];
            $data['genres'] = [];

            $movie = $tmdb->getMovie($data['tmdbID']);
            $genres = $movie->getGenres();
            foreach ($genres as $genre) {
                $genreID = $genre->getId();
                $genreName = $genre->getName();

                $array = array(
                    'id' => $genreID,
                    'name' => $genreName,
                );

                $data['genres'][] = $array;
            }
            $data['file_path'] = $row['file_path'];
        }
    } else {
        $data = 0;
    }

    $conn->close();
    return $data;
}

//-- Returns all local database movies ordered by A-Z or Z-A --
function selectAllMoviesByTitle($order = '') {
    $conn = dbConnect();
    $movies = [];
    $i = 0;

    if ( $order != '' ) {
        $sql = "SELECT tmdbID, title, tagline, overview, poster, backdrop, rating, releaseDate, runtime, genres, mediaType FROM media WHERE mediaType='movie' ORDER BY title $order";
    } else {
        $sql = "SELECT tmdbID, title, tagline, overview, poster, backdrop, rating, releaseDate, runtime, genres, mediaType FROM WHERE mediaType='movie'";
    }
    
    $results = $conn->query($sql);
    
    if ( $results->num_rows > 0 ) {
        while ( $movie = $results->fetch_assoc() ) {
            $movies[$i] = $movie;
            $i++;
        }        
    }

    $conn->close();
    return $movies;
}

//-- Returns all local database movies ordered by A-Z or Z-A --
function selectMovieByTitle($title){
    $conn = dbConnect();
    $movies = [];

    if ( $title !== '' ) {
        $sql = "SELECT tmdbID, title, tagline, overview, poster, backdrop, rating, releaseDate, runtime, movieCollection, genres FROM media WHERE title LIKE '%$title%' AND mediaType='movie'";
        $results = $conn->query($sql);
        
        if ( $results->num_rows > 0 ) {
            $movies[] = $results->fetch_assoc();
        }
    }

    $conn->close();
    return $movies;
}

//-- Checks if the movie is already in local database so it wont show up in movie collections --
function movieIsInCollection($movieID){
    $conn = dbConnect();

    $sql = "SELECT id FROM media WHERE tmdbID='$movieID'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $conn->close();
        return true;
    }
}

//-- Updates the filepath of movie sources --
function updateMovieFilePath($moviePath, $movieID) {
    $conn = dbConnect();
    $moviePath = mysqli_real_escape_string($conn, $moviePath);

    $sql = "UPDATE media SET file_path='$moviePath' WHERE tmdbID='$movieID'";

    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_file_apth_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
        $conn->close();
        set_callout('success','update_file_path_success');
        page_redirect('/admin/movie/?id='.$movieID);
    }
}

//-- Updates the previewd poster image of movies --
function updateMoviePoster($movieID, $poster) {
    $conn = dbConnect();
    $posterPATH = mysqli_real_escape_string($conn, $poster);

    $sql = "UPDATE media SET poster='$posterPATH' WHERE tmdbID='$movieID'";
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_poster_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
        $conn->close();
        set_callout('success','update_poster_success');
        page_redirect('/admin/movie/?id='.$movieID);
    }
}

//-- Updates the previewd backdrop image of movies --
function updateMovieBackdrop($movieID, $backdrop) {
    $conn = dbConnect();
    $backdropPATH = mysqli_real_escape_string($conn, $backdrop);

    $sql = "UPDATE media SET thumbnail='$backdropPATH' WHERE tmdbID='$movieID'";
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_backdrop_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
        $conn->close();
        set_callout('success','update_backdrop_success');
        page_redirect('/admin/movie/?id='.$movieID);
    }
}

//-- Updates the previewd backdrop image of movies --
function updateMovieTrailer($movieID, $trailer) {
    $conn = dbConnect();
    $trailerID = mysqli_real_escape_string($conn, $trailer);

    $sql = "UPDATE media SET trailer='$trailerID' WHERE tmdbID='$movieID'";
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_trailer_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
        $conn->close();
        set_callout('success','update_trailer_success');
        page_redirect('/admin/movie/?id='.$movieID);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////// SHOWS

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
        $showQuery = "INSERT INTO media (
            tmdbID,
            title,
            overview,
            poster,
            backdrop,
            rating,
            releaseDate,
            genres,
            show_season_count,
            show_seasons,
            show_episodes_count,
            mediaType
        ) VALUES (
            $id,
            '$title',
            '$overview',
            '$poster',
            '$backdrop',
            $rating,
            '$release',
            '$genresString',
            $seasonsCount,
            '$seasonsIDs',
            $episodesCount,
            'show'
        )";
        $conn->query($showQuery);

        foreach($genres as $genre) {
            $genreID = $genre->getId();
    
            // Füge die Genre-Verbindung in die "show_genre"-Tabelle ein
            $genreQuery = "INSERT INTO media_genre (media_id, genre_id) VALUES ($id, $genreID)";
            $conn->query($genreQuery);
        }

        foreach ( $seasons as $season ) {
            $seasonID = $season->getID();
            $seasonNumber = $season->getSeasonNumber();
            $seasonEpisodeCount = $season->getEpisodeCount();
            $seasonTitle = str_replace("'", '"', $season->getName());
            $seasonOverview = str_replace("'", '"', $season->getOverview());
            $seasonPoster = $season->getPoster();
            $seasonRating = $season->getVoteAverage();
            $seasonRelease = $season->getAirDate();
    
            // Adds all Seasons of the show
            $sqlSeasons = "INSERT INTO seasons (tmdbID, title, overview, poster, season_number, rating, releaseDate, episodes_count, show_tmdbID) VALUES ('".$seasonID."','".$seasonTitle."','".$seasonOverview."','".$seasonPoster."','".$seasonNumber."','".$seasonRating."','".$seasonRelease."','".$seasonEpisodeCount."','".$id."');";
            $conn->query($sqlSeasons);

            $actualSeason = $tmdb->getSeason($id, $seasonNumber);
            $seasonEpisodes = $actualSeason->getEpisodes();

            foreach ( $seasonEpisodes as $seasonEpisode ) {
                $episodeNumber = $seasonEpisode->getEpisodeNumber();

                $episode = $tmdb->getEpisode($id, $seasonNumber, $episodeNumber);
                $episodeID = $episode->getID();
                $episodeNumber = $episode->getEpisodeNumber();
                $episodeName = str_replace("'", '"', $episode->getName());
                $episodeOverview = str_replace("'", '"', $episode->getOverview());
                $episodeRuntime = $episode->getRuntime();
                $episodeSeasonNumber = $seasonNumber;
                $episodeShowID = $id;
                $episodeRelease = $episode->getAirDate();
                $episodeImgPath = $episode->getStill();
                $episodeRating = $episode->getVoteAverage();

                // Adds all episodes of the show
                $episodeQuery = "INSERT INTO episodes (tmdbID,episode_number,title,overview,backdrop,runtime,rating,releaseDate,season_number,show_id) VALUES ('".$episodeID."','".$episodeNumber."','".$episodeName."','".$episodeOverview."','".$episodeImgPath."','".$episodeRuntime."','".$episodeRating."','".$episodeRelease."','".$episodeSeasonNumber."','".$episodeShowID."');";
                $conn->query($episodeQuery);
            }
        }

        // Commit der Transaktion
        $conn->commit();
        $conn->close();
        set_callout('success','add_show_success');
        page_redirect("/admin/shows");
    } catch (Exception $e) {
        // Bei einem Fehler Rollback der Transaktion
        $conn->rollback();
        set_callout('alert','add_show_alert');
        page_redirect("/admin/shows");
    }
}

function selectAllShowsByTitle($order = '') {
    $conn = dbConnect();
    $shows = [];
    $i = 0;

    if ( $order != '' ) {
        $sql = "SELECT tmdbID, title, overview, poster, backdrop, rating, releaseDate, genres, show_season_count, mediaType FROM media WHERE mediaType='show' ORDER BY title $order";
    } else {
        $sql = "SELECT tmdbID, title, overview, poster, backdrop, rating, releaseDate, genres, show_season_count, mediaType FROM media WHERE mediaType='show'";
    }
    
    $results = $conn->query($sql);

    if ( $results->num_rows > 0 ) {
        while ( $show =  $results->fetch_assoc() ) {
            $shows[$i] = $show;
            $i++;
        }
    }

    $conn->close();
    return $shows;
}

//-- Returns all information of a movie from local database --
function selectShowByID($showID) {
    $tmdb = setupTMDB();
    $conn = dbConnect();

    $sql = "SELECT tmdbID, title, overview, poster, backdrop, rating, releaseDate, genres, trailer FROM media WHERE tmdbID='$showID'";
    $result = $conn->query($sql);

    $data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data['tmdbID'] = $row['tmdbID'];
            $data['title'] = $row['title'];
            $data['backdrop'] = $row['backdrop'];
            $data['poster'] = $row['poster'];         
            $data['overview'] = $row['overview'];
            $data['rating'] = $row['rating'];
            $data['release'] = $row['releaseDate'];
            $data['trailer'] = $row['trailer'];
            $data['genres'] = [];

            $show = $tmdb->getTVShow($data['tmdbID']);
            $genres = $show->getGenres();
            foreach ($genres as $genre) {
                $genreID = $genre->getId();
                $genreName = $genre->getName();

                $array = array(
                    'id' => $genreID,
                    'name' => $genreName,
                );

                $data['genres'][] = $array;
            }
        }
    } else {
        $data = 0;
    }

    $conn->close();
    return $data;
}

//-- Updates the previewd poster image of movies --
function updateShowPoster($showID, $poster) {
    $conn = dbConnect();
    $posterPATH = mysqli_real_escape_string($conn, $poster);

    $sql = "UPDATE media SET poster='$posterPATH' WHERE tmdbID='$showID'";
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_poster_alert');
        page_redirect('/admin/movie/?id='.$showID);
    } else {
        $conn->close();
        set_callout('success','update_poster_success');
        page_redirect('/admin/movie/?id='.$showID);
    }
}

//-- Updates the previewd backdrop image of movies --
function updateShowBackdrop($showID, $backdrop) {
    $conn = dbConnect();
    $backdropPATH = mysqli_real_escape_string($conn, $backdrop);

    $sql = "UPDATE media SET thumbnail='$backdropPATH' WHERE tmdbID='$showID'";
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_backdrop_alert');
        page_redirect('/admin/movie/?id='.$showID);
    } else {
        $conn->close();
        set_callout('success','update_backdrop_success');
        page_redirect('/admin/movie/?id='.$showID);
    }
}

//-- Updates the previewd backdrop image of movies --
function updateShowTrailer($showID, $trailer) {
    $conn = dbConnect();
    $trailerID = mysqli_real_escape_string($conn, $trailer);

    $sql = "UPDATE media SET trailer='$trailerID' WHERE tmdbID='$showID'";
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_trailer_alert');
        page_redirect('/admin/movie/?id='.$showID);
    } else {
        $conn->close();
        set_callout('success','update_trailer_success');
        page_redirect('/admin/movie/?id='.$showID);
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////// Media Card
function media_card($media, $extraClasses = '') {
    $conn = dbConnect();

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
    $extras = '';

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
                // Generate season select
                // since season 0 is always extras, it will be added at the end
                if ( $seasonRow['season_number'] === '0' ) {
                    $extras = '<option value="'.$seasonRow['season_number'].'">'.$seasonRow['title'].'</option>';
                } else {
                    $options = $options.'<option value="'.$seasonRow['season_number'].'">'.$seasonRow['title'].'</option>';
                    $seasonWrap = $seasonWrap.'<div class="col12" data-season="'.$seasonRow['season_number'].'"></div>';
                } 
            }

            $select = '<p><select class="season-select" id="season-select-'.$mediaID.'">'.$options.$extras.'</select></p>';
        }
    }

    

    $genres = json_decode($media['genres']);
    $genreHTML = '';
    foreach ( $genres as $genre ) {
        $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
    }

    $userID = intval($_SESSION['userID']);

    // Generate Season - Episode list for the show
    $episodesContainer = 
    '<div class="episodes-wrap" id="media-'.$mediaID.'-episode-wrap">
    
    </div>';

    
    $watchingSQL = "SELECT watched_seconds, total_length FROM media_watched WHERE user_id = $userID and media_id = $mediaID and watched_seconds > 0";
    $watchInfos = $conn->query($watchingSQL);
    if ( $watchInfos->num_rows > 0 ) {
        while ( $watchInfo = $watchInfos->fetch_assoc() ) {
            $watchedInPercent = getWatchedTime($watchInfo['watched_seconds'], $watchInfo['total_length']);
        }
        $timebar = '<div class="watched-bar"><progress max="100" value="'.$watchedInPercent.'"></progress></div>';
    } else {
        $timebar = '';
    }
    
    //-- Watch list --
    $watchListCheckSQL = "SELECT id FROM watchlist WHERE user_id=$userID and media_id=$mediaID";

    if ( $conn->query($watchListCheckSQL)->num_rows > 0 ) {
        $listButtons = '
        <a href="#" class="btn btn-small btn-white icon-left icon-add mylist-btn add-to-list hidden loading" data-media-id="'.$mediaID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-small btn-white icon-left icon-remove mylist-btn remove-from-list loading" data-media-id="'.$mediaID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    } else {
        $listButtons = '
        <a href="#" class="btn btn-small btn-white icon-left icon-add mylist-btn add-to-list loading" data-media-id="'.$mediaID.'" data-type="add">'.lang_snippet('my_list').'</a>
        <a href="#" class="btn btn-small btn-white icon-left icon-remove mylist-btn remove-from-list hidden loading" data-media-id="'.$mediaID.'" data-type="remove">'.lang_snippet('my_list').'</a>';
    }

    if ( $_SESSION['role'] === "1" ) {
        if ( $type === 'movie' ) {
            $editBtn = '<a href="/admin/movie/?id='.$mediaID.'" title="'.lang_snippet('edit').'" class="edit-trigger"></a>';
        } else if ( $type === 'show' ) {
            $editBtn = '<a href="/admin/show/?id='.$mediaID.'" title="'.lang_snippet('edit').'" class="edit-trigger"></a>';
        }
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
                        <a href="/watch/?id='.$mediaID.'" title="'.$title.'" class="play-trigger"></a>
                        <a href="#content-'.$mediaID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$mediaID.'"></a>
                        '.$editBtn.'
                    </div>
                </div>
                '.$timebar.'

                <div class="info-popup" id="content-'.$mediaID.'" style="display:none;">
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
                                <span class="tag">'.$extraInfo.'</span>
                            </p>
                            <a href="/watch/?id='.$mediaID.'" class="btn btn-small btn-white icon-left icon-play marg-right-xs">Jetzt schauen</a>
                            '.$listButtons.'
                            <p class="small">'.$overview.'</p>
                            <p class="small tag-list marg-bottom-base">'.$genreHTML.'</p>
                            '.$select.$seasonWrap.'
                            '.getTrailer($mediaID, 'marg-top-xs marg-bottom-xs').'
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

function getTrailer($movieID, $extraClass="") {
    $conn = dbConnect();
    $sql = "SELECT trailer FROM media WHERE tmdbID=$movieID";
    $result = $conn->query($sql)->fetch_assoc();
    
    if ( isset($result['trailer']) ) {
        $trailerID = $result['trailer'];
        $iframe = '<figure class="widescreen '.$extraClass.'"><iframe id="ytplayer-'.$movieID.'" type="text/html" src="http://www.youtube.com/embed/'.$trailerID.'?enablejsapi=1" frameborder="0"></iframe></figure>';

        $conn->close();
        return $iframe;
    }
}