<?php
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
        $movieQuery = "INSERT INTO movies (
                movie_tmdbID,
                movie_title,
                movie_tagline,
                movie_overview,
                movie_poster,
                movie_thumbnail,
                movie_rating,
                movie_release,
                movie_runtime,
                movie_collection,
                movie_genres
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
                '$genresString'
        )";

        $conn->query($movieQuery);

        foreach($genres as $genre) {
            $genreID = $genre->getId();
    
            // Füge die Genre-Verbindung in die "movie_genre"-Tabelle ein
            $genreQuery = "INSERT INTO movie_genre (movie_id, genre_id) VALUES ($id, $genreID)";
            $conn->query($genreQuery);
        }
        
        // Adds to media table
        $mediaQuery = "INSERT INTO media (tmdbID, type) VALUES ($id, 'movie')";
        $conn->query($mediaQuery);

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
    $sql = "SELECT id FROM movies WHERE movie_tmdbID = $movieID";
    $data = $conn->query($sql)->fetch_assoc();
    $id = $data['id'];

    $conn->begin_transaction();

    try {
        $genreDeleteQuery = "DELETE FROM movie_genre WHERE movie_id = $id";
        $conn->query($genreDeleteQuery);

        $highlightDeleteQuery = "DELETE FROM highlights WHERE movie_id = $id";
        $conn->query($highlightDeleteQuery);

        $mediaDeleteQuery = "DELETE FROM media WHERE tmdbID = $id";
        $conn->query($mediaDeleteQuery);

        // Lösche den Film aus der movies-Tabelle
        $filmDeleteQuery = "DELETE FROM movies WHERE id = $id";
        $conn->query($filmDeleteQuery);

        // Überprüfe, ob die Löschvorgänge erfolgreich waren
        if ($conn->affected_rows > 0) {
            // Die Löschungen waren erfolgreich
            // Commit der Transaktion
            $conn->commit();
            $conn->close();
            set_callout('success','delete_movie_success');
            page_redirect("/admin/movies");
        } else {
            // Keine entsprechenden Einträge gefunden oder Löschungen fehlgeschlagen
            // Rollback der Transaktion
            $conn->rollback();
            $conn->close();
            set_callout('alert','delete_movie_alert');
            page_redirect('/admin/movie/?id='.$movieID);
        }
    } catch (Exception $e) {
        // Bei einem Fehler Rollback der Transaktion
        $conn->rollback();
        $conn->close();
        set_callout('alert','delete_movie_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    }
}

//-- Returns all information of a movie from local database --
function selectMovieByID($movieID) {
    $tmdb = setupTMDB();
    $conn = dbConnect();

    $sql = "SELECT movie_tmdbID, movie_title, movie_tagline, movie_overview, movie_poster, movie_thumbnail, movie_rating, movie_release, movie_runtime, movie_collection, movie_file_path, movie_genres, movie_trailer FROM movies WHERE movie_tmdbID='$movieID'";
    $result = $conn->query($sql);

    $data = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data['id'] = $row['movie_tmdbID'];
            $data['title'] = $row['movie_title'];
            $data['backdrop'] = $row['movie_thumbnail'];
            $data['poster'] = $row['movie_poster'];
            if ( !is_null($row['movie_tagline']) ) {
                $data['tagline'] = $row['movie_tagline'];
            } else {
                $data['tagline'] = '';
            }            
            $data['overview'] = $row['movie_overview'];
            $data['voteAverage'] = $row['movie_rating'];
            $data['release'] = $row['movie_release'];
            $data['runtime'] = intval($row['movie_runtime']);
            if ( !is_null($row['movie_collection']) ) {
                $data['collection'] = intval($row['movie_collection']);
            } else {
                $data['collection'] = '';
            }
            $data['trailer'] = $row['movie_trailer'];
            $data['genres'] = [];

            $movie = $tmdb->getMovie($data['id']);
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
            $data['file_path'] = $row['movie_file_path'];
        }
    } else {
        $data = 0;
    }

    $conn->close();
    return $data;
}

//-- Check if movie is in local database --
function movieInLocalDB($movieID) {
    $conn = dbConnect();

    $sql = "SELECT movie_tmdbID FROM movies WHERE movie_tmdbID='$movieID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $conn->close();
        return true;
    }
}

//-- Returns all local database movies ordered by A-Z or Z-A --
function selectAllMoviesByTitle($order = '') {
    $conn = dbConnect();
    
    if ( $order != '' ) {
        $sql = "SELECT movie_tmdbID, movie_title, movie_tagline, movie_overview, movie_poster, movie_thumbnail, movie_rating, movie_release, movie_runtime, movie_genres FROM movies ORDER BY movie_title $order";
    } else {
        $sql = "SELECT movie_tmdbID, movie_title, movie_tagline, movie_overview, movie_poster, movie_thumbnail, movie_rating, movie_release, movie_runtime, movie_genres FROM movies";
    }
    
    $results = $conn->query($sql);

    if ( $results->num_rows > 0 ) {
            
        $movies = [];
        while ($rows = $results->fetch_assoc() ) {
            $movies[] = moviesDataconverter($rows);
        }
    }

    $conn->close();
    return $movies;
}

//-- Returns all local database movies ordered by A-Z or Z-A --
function selectMovieByTitle($title){
    $conn = dbConnect();
    $data = '';

    if ( $title !== '' ) {
        $sql = "SELECT movie_tmdbID, movie_title, movie_tagline, movie_overview, movie_poster, movie_thumbnail, movie_rating, movie_release, movie_runtime, movie_collection, movie_genres FROM movies WHERE movie_title LIKE '%$title%'";
        $results = $conn->query($sql);
        
        if ( $results->num_rows > 0 ) {

            $movies = [];
            while ($rows = $results->fetch_assoc() ) {
                $movies[] = moviesDataconverter($rows);
            }
        } else {
            return $data;
        }

        $conn->close();
        return $movies;
    } else {
        $conn->close();
        return $data;
    }
}

//-- Checks if the movie is already in local database so it wont show up in movie collections --
function movieIsInCollection($id){
    $conn = dbConnect();

    $sql = "SELECT id FROM movies WHERE movie_tmdbID='$id'";
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

    $sql = "UPDATE movies SET movie_file_path='$moviePath' WHERE movie_tmdbID='$movieID'";

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

    $sql = "UPDATE movies SET movie_poster='$posterPATH' WHERE movie_tmdbID='$movieID'";
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

    $sql = "UPDATE movies SET movie_thumbnail='$backdropPATH' WHERE movie_tmdbID='$movieID'";
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

    $sql = "UPDATE movies SET movie_trailer='$trailerID' WHERE movie_tmdbID='$movieID'";
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

//-- Movie data converter --
function moviesDataconverter($row) {
    $data['tmdbID'] = $row['movie_tmdbID'];
    $data['title'] = $row['movie_title'];            
    $data['tagline'] = $row['movie_tagline'];
    $data['overview'] = $row['movie_overview'];
    $data['poster'] = $row['movie_poster'];
    $data['backdrop'] = $row['movie_thumbnail'];
    $data['rating'] = $row['movie_rating'];
    $data['release'] = $row['movie_release'];
    $data['runtime'] = $row['movie_runtime'];
    $data['genres'] = $row['movie_genres'];
    
    return $data;
}
?>