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

    $sql = "DELETE FROM media_watched WHERE show_id=$movieID;";
    $sql .= "DELETE FROM watchlist WHERE media_id=$movieID;";
    $sql .= "DELETE FROM media_genre WHERE media_id=$movieID;";
    $sql .= "DELETE FROM highlights WHERE highlight_id=$movieID;";
    $sql .= "DELETE FROM media WHERE tmdbID=$movieID;";

    if (!($conn->multi_query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','delete_movie_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
        $conn->close();
        set_callout('success','delete_movie_success');
        page_redirect('/admin/movies');
    }   
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
            $genres = json_decode($row['genres']);
        
            foreach ($genres as $genre_id) {
                $genreSQL = "SELECT genre_id, genre_name FROM genres WHERE genre_id = $genre_id;";
                $genreResult = $conn->query($genreSQL);

                while ( $genre = $genreResult->fetch_assoc() ) {
                    $genreID = $genre['genre_id'];
                    $genreName = $genre['genre_name'];
                }                

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
        $sql = "SELECT tmdbID, title, tagline, overview, poster, backdrop, rating, file_path, releaseDate, runtime, genres, mediaType FROM media WHERE mediaType='movie' ORDER BY title $order";
    } else {
        $sql = "SELECT tmdbID, title, tagline, overview, poster, backdrop, rating, file_path, releaseDate, runtime, genres, mediaType FROM WHERE mediaType='movie'";
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

    $sql = "UPDATE media SET backdrop='$backdropPATH' WHERE tmdbID='$movieID'";
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

    $dataSeasonSQLQuerys = [];
    $dataEpisodesSQLquery = [];

    foreach ( $seasons as $season ) {
        $seasonID = $season->getID();
        $dataSeasonsIDs[] = $seasonID;        
        $seasonNumber = $season->getSeasonNumber();
        $seasonEpisodeCount = $season->getEpisodeCount();
        $seasonTitle = str_replace("'", '"', $season->getName());
        $seasonOverview = str_replace("'", '"', $season->getOverview());
        $seasonPoster = $season->getPoster();
        $seasonRating = $season->getVoteAverage();
        $seasonRelease = $season->getAirDate();

        // Adds all Seasons of the show
        $dataSeasonSQLQuerys[] = "INSERT INTO seasons (tmdbID, title, overview, poster, season_number, rating, releaseDate, episodes_count, show_tmdbID) VALUES ('".$seasonID."','".$seasonTitle."','".$seasonOverview."','".$seasonPoster."','".$seasonNumber."','".$seasonRating."','".$seasonRelease."','".$seasonEpisodeCount."','".$id."');";
        
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
            $dataEpisodesSQLQuerys[] = "INSERT INTO episodes (tmdbID,episode_number,title,overview,backdrop,runtime,rating,releaseDate,season_number,show_id) VALUES ('".$episodeID."','".$episodeNumber."','".$episodeName."','".$episodeOverview."','".$episodeImgPath."','".$episodeRuntime."','".$episodeRating."','".$episodeRelease."','".$episodeSeasonNumber."','".$episodeShowID."');";    
        }
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

        foreach ( $dataSeasonSQLQuerys as $seasonSQLQuery ) {
            $conn->query($seasonSQLQuery);
        }

        foreach ( $dataEpisodesSQLQuerys as $episodesSQLQuery ) {
            $conn->query($episodesSQLQuery);
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

function deleteShow($showID) {
    $conn = dbConnect();

    $sql = "DELETE FROM media_watched WHERE show_id=$showID;";
    $sql .= "DELETE FROM watchlist WHERE media_id=$showID;";
    $sql .= "DELETE FROM episodes WHERE show_id=$showID;";
    $sql .= "DELETE FROM seasons WHERE show_tmdbID=$showID;";
    $sql .= "DELETE FROM media_genre WHERE media_id=$showID;";
    $sql .= "DELETE FROM highlights WHERE highlight_id=$showID;";
    $sql .= "DELETE FROM media WHERE tmdbID=$showID;";

    if (!($conn->multi_query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','delete_show_alert');
        page_redirect('/admin/shows');
    } else {
        $conn->close();
        set_callout('success','delete_show_success');
        page_redirect('/admin/shows');
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
            $genres = json_decode($row['genres']);
        
            foreach ($genres as $genre_id) {
                $genreSQL = "SELECT genre_id, genre_name FROM genres WHERE genre_id = $genre_id;";
                $genreResult = $conn->query($genreSQL);

                while ( $genre = $genreResult->fetch_assoc() ) {
                    $genreID = $genre['genre_id'];
                    $genreName = $genre['genre_name'];
                }                

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
        page_redirect('/admin/show/?id='.$showID);
    } else {
        $conn->close();
        set_callout('success','update_poster_success');
        page_redirect('/admin/show/?id='.$showID);
    }
}

//-- Updates the previewd backdrop image of movies --
function updateShowBackdrop($showID, $backdrop) {
    $conn = dbConnect();
    $backdropPATH = mysqli_real_escape_string($conn, $backdrop);

    $sql = "UPDATE media SET backdrop='$backdropPATH' WHERE tmdbID='$showID'";
    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_backdrop_alert');
        page_redirect('/admin/show/?id='.$showID);
    } else {
        $conn->close();
        set_callout('success','update_backdrop_success');
        page_redirect('/admin/show/?id='.$showID);
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
        page_redirect('/admin/show/?id='.$showID);
    } else {
        $conn->close();
        set_callout('success','update_trailer_success');
        page_redirect('/admin/show/?id='.$showID);
    }
}

function updateShow($showID) {
    $conn = dbConnect();
    $tmdb = setupTMDB();

    $conn->begin_transaction();

    $id = $showID;
    $show = $tmdb->getTVShow($id);
    $seasons = $show->getSeasons();
    $seasonsCount = $show->getNumSeasons();
    $episodesCount = $show->getNumEpisodes();

    $dataSeasonsIDs = [];
    
    foreach ( $seasons as $season ) {
        $dataSeasonsIDs[] = $season->getID();
    }
    
    $seasonsIDs = json_encode($dataSeasonsIDs);

    try {
        // Füge den neuen Film in die "shows"-Tabelle ein
        $showQuery = "UPDATE media SET
            show_season_count = $seasonsCount,
            show_seasons = '$seasonsIDs',
            show_episodes_count = $episodesCount,
            WHERE tmdbID = $id AND mediaType='show';";
        $conn->query($showQuery);

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
        page_redirect("/admin/show/?id=$id");
    } catch (Exception $e) {
        // Bei einem Fehler Rollback der Transaktion
        $conn->rollback();
        set_callout('alert','add_show_alert');
        page_redirect("/admin/shows/?id=$id");
    }
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////// Episodes

//-- Updates the filepath of movie sources --
function updateEpisodeFilePath($episodePath, $episodeID, $showID) {
    $conn = dbConnect();
    $episodePath = mysqli_real_escape_string($conn, $episodePath);

    $sql = "UPDATE episodes SET file_path='$episodePath' WHERE tmdbID='$episodeID'";

    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert','update_file_apth_alert');
        page_redirect('/admin/show/?id='.$showID);
    } else {
        $conn->close();
        set_callout('success','update_file_path_success');
        page_redirect('/admin/show/?id='.$showID);
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
    $cnf = tmdbConfig();

    $userID = intval($_SESSION['userID']);
    $mediaID = $media['tmdbID'];
    $title = $media['title'];
    $poster = $media['poster'];
    $backdrop = $media['backdrop'];
    $type = $media['mediaType'];
    $editBtn = '';

    ////-- Adds edit btn when user is admin --////
    if ( $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin' ) {
        if ( $cnf['enable_edit_btn'] === 'checked') {
            if ( $type === 'movie' ) {
                $editBtn = '<a href="/admin/movie/?id='.$mediaID.'" title="'.lang_snippet('edit').'" class="edit-trigger"></a>';
            } else if ( $type === 'show' ) {
                $editBtn = '<a href="/admin/show/?id='.$mediaID.'" title="'.lang_snippet('edit').'" class="edit-trigger"></a>';
            }
        }
    }

    ////-- Play buttons and time progressbar --////
    $watchTrigger= '';
    $timebar = '';
    $disabled = '';
    
    if ( $type === 'movie' ) {
        $disabled = 'disabled';
        
        // Add play btn when media has file path
        if ( $media['file_path'] != "" ) {
            $watchTrigger = '<a href="/watch/?id='.$mediaID.'" title="'.$title.'" class="play-trigger"></a>';
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

        if ( $currEpisodeResult->num_rows > 0 ) {
            while ( $currEpisode = $currEpisodeResult->fetch_assoc() ) {
                $episodeID = $currEpisode['media_id'];
                $currEpisodeTime = getWatchedTime($currEpisode['watched_seconds'], $currEpisode['total_length']);
            }

            // Sets time bar for last episode watched
            $timebar = '<div class="watched-bar"><progress max="100" value="'.$currEpisodeTime.'"></progress></div>';

            // Sets play button for last episode watched
            $getSeason = "SELECT title, season_number FROM episodes WHERE tmdbID = ".$episodeID." AND show_id = $mediaID";
            $getSeasonResult = $conn->query($getSeason);

            while ( $currSeason = $getSeasonResult->fetch_assoc() ) {
                $seasonNr = $currSeason['season_number'];
            }

            $watchTrigger = '<a href="/watch/?s='.$seasonNr.'&id='.$mediaID.'" title="'.$title.'" class="play-trigger"></a>';
        } else {
            // Adds watch progress bar show
            $firstEpisodeSQL = "SELECT tmdbID, file_path FROM episodes WHERE show_id = $mediaID AND season_number = 1 AND episode_number = 1";
            $firstEpisodeResult = $conn->query($firstEpisodeSQL);

            if ( $firstEpisodeResult->num_rows > 0 ) {
                while ( $firstEpisode = $firstEpisodeResult->fetch_assoc() ) {
                    if ( $firstEpisode['file_path'] != NULL ) {
                        // Sets play button for first episode of show
                        $watchTrigger = '<a href="/watch/?s=1&id='.$firstEpisode['tmdbID'].'" title="'.$title.'" class="play-trigger"></a>';
                    } else {
                        $disabled = 'disabled';
                    }
                }
            } else {
                $disabled = 'disabled';
            }
        }
    }

    ////-- Generated output --////
    $card = '
        <div class="'.$extraClasses.'">
            <div class="media-card '.$disabled.'">
                <div class="media-card-wrapper">
                    <figure class="widescreen desktop-only">
                        <img data-img="'.loadImg('original', $backdrop).'" loading="lazy" importance="low" alt="'.$title.'">
                    </figure>
                    <figure class="poster mobile-only">
                        <img data-img="'.loadImg('original', $poster).'" loading="lazy" importance="low" alt="'.$title.'">
                    </figure>
                    <div class="link-wrapper">
                    '.$watchTrigger.'
                    <a href="#content-'.$mediaID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="'.$mediaID.'"></a>
                    '.$editBtn.'
                    </div>
                </div>
                '.$timebar.'
            </div>
        </div>';

    $conn->close();
    return $card;
}

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////// Extras

// Get trailer
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

function getWatchedTime($watchedTime, $totalDuration) {
    $watchedTime = floatval($watchedTime);
    $totalDuration = floatval($totalDuration);
    $watchedInPercent = ($watchedTime/$totalDuration)*100;

    return $watchedInPercent;
}