<?php
//-- Saves movie with informations in database -- 
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
        // Füge den neuen Film in die "movies"-Tabelle ein
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
    
            // Füge die Genre-Verbindung in die "movie_genre"-Tabelle ein
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
        set_callout('success','add_movie_success');
        //page_redirect("/admin/shows");
    } catch (Exception $e) {
        // Bei einem Fehler Rollback der Transaktion
        $conn->rollback();
        set_callout('alert','add_movie_alert');
        //page_redirect("/admin/shows");
    }
}