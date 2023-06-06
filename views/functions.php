<?php
require_once ROOT_PATH.'/admin/language.php';

//-- Returns TMDB Class --
function setupTMDB() {
    include ROOT_PATH.'/tmdb/configuration/default.php';
    require_once ROOT_PATH.'/tmdb/tmdb-api.php';
    $tmdb = new TMDB($cnf);

    return $tmdb;
}

//-- Set Browserlanguage --
function get_browser_language( $available = [], $default = 'en' ) {
	if ( isset( $_SERVER[ 'HTTP_ACCEPT_LANGUAGE' ] ) ) {

		$langs = explode( ',', $_SERVER['HTTP_ACCEPT_LANGUAGE'] );

		if ( empty( $available ) ) {
		  return empty( $langs ) ? $default : $langs[ 0 ];
		}

		foreach ( $langs as $lang ){
			$lang = substr( $lang, 0, 2 );
			if( in_array( $lang, $available ) ) {
				return $lang;
			}
		}
	}
	return $default;
}

//-- Set colormode --
function setTheme() {
	$themeCookie = 'pageTheme';
	
	if( !isset( $_COOKIE[$themeCookie] ) ) {
		$theme = '';
	} else {
		$theme = $_COOKIE[$themeCookie];
	}

	$expireDate = time() + (86400 * 365);

	if( empty( $theme ) ) {
		setcookie($themeCookie, 'light', $expireDate, '/');
		$theme = 'data-theme="light"';
	} else if ( $theme === 'light' ) {
		setcookie($themeCookie, 'light', $expireDate, '/');
		$theme = 'data-theme="light"';
	} else if ( $theme !== 'light' ) {
		setcookie($themeCookie, $theme, $expireDate, '/');
		$theme = 'data-theme="'.$theme.'"';
	}

	echo $theme;
}

//-- Callout --
function set_callout($type, $message) {
    setcookie('callout_type', $type);
    setcookie('callout_message', $message);
}

function callout() {
    if (isset($_COOKIE['callout_type']) && isset($_COOKIE['callout_message'])) {
        $type = $_COOKIE['callout_type'];
        $message = lang_snippet($_COOKIE['callout_message']);

        echo '<div class="callout '.$type.'">';
            echo '<p>'.$message.'</p>';
        echo '</div>';

        unset($_COOKIE['callout_type']);
        unset($_COOKIE['callout_message']); 
        setcookie('callout_type', '', time() - 3600); 
        setcookie('callout_message', '', time() - 3600);
    }
}

//-- Loads the backend menu --
function get_backend_menu() {
	return include(ROOT_PATH.'/views/includes/backend-menu.php');
}

function get_apikey_db() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT setting_option FROM settings WHERE setting_name='apikey'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['setting_option'];
        }
    }
}

//-- String trimmer
function truncate($string,$length=100,$append=" ...") {
    $string = trim($string);

    if(strlen($string) > $length) {
      $string = wordwrap($string, $length);
      $string = explode("\n", $string, 2);
      $string = $string[0] . $append;
    }
  
    return $string;
}

//-- Saves movie with informations in database -- 
function insert_movie($movieID) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $tmdb = setupTMDB();

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
    $collection = mysqli_real_escape_string($conn, $movie->getCollection());
    $genres = $movie->getGenres();

    $movieGenres = [];
    foreach($genres as $genre) {
        $movieGenres[] = $genre->getId();
    }

    $genres = json_encode($movieGenres);

    $sql = 'INSERT INTO movies (
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
            "'.$id.'",
            "'.$title.'",
            "'.$tagline.'",
            "'.$overview.'",
            "'.$poster.'",
            "'.$backdrop.'",
            "'.$rating.'",
            "'.$release.'",
            "'.$runtime.'",
            "'.$collection.'",
            "'.$genres.'"
        )';
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','add_movie_alert');
        header('Location: /movies');
        exit();
    } else {
        set_callout('success','add_movie_success');
        header('Location: /movies/edit-movie/?id='.$id);
        exit();
    }
}

//-- Returns all information of a movie from local database --
function selectMovieByID($movieID) {
    $tmdb = setupTMDB();
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM movies WHERE movie_tmdbID='".$movieID."'";
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

    return $data;
}

//-- Returns all local database movies ordered by A-Z or Z-A --
function selectAllMoviesByTitle($order = ''){
    $tmdb = setupTMDB();
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM movies ORDER BY movie_title $order";
    $result = $conn->query($sql);

    $data = [];
    $i = 0;

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {

            $data[$i]['id'] = $row['movie_tmdbID'];
            $data[$i]['title'] = $row['movie_title'];            
            $data[$i]['tagline'] = $row['movie_tagline'];
            $data[$i]['overview'] = $row['movie_overview'];
            $data[$i]['poster'] = $row['movie_poster'];
            $data[$i]['backdrop'] = $row['movie_thumbnail'];
            $data[$i]['voteAverage'] = $row['movie_rating'];
            $data[$i]['release'] = $row['movie_release'];
            $data[$i]['runtime'] = $row['movie_runtime'];
            $data[$i]['collection'] = $row['movie_collection'];
            $data[$i]['genres'] = [];
            
            $movie = $tmdb->getMovie($data[$i]['id']);
            $genres = $movie->getGenres();
            foreach ($genres as $genre) {
                $genreID = $genre->getId();
                $genreName = $genre->getName();

                $array = array(
                    'id' => $genreID,
                    'name' => $genreName,
                );

                $data[$i]['genres'][] = $array;
            }

            $i++;
        }
    }

    return $data;
}

//-- Checks if the movie is already in local database so it wont show up in movie collections --
function movie_is_in_collection($id){
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM movies WHERE movie_tmdbID='".$id."'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return true;
    }
}

//-- Updates the filepath of movie sources --
function updateMovieFilePath($moviePath, $movieID) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = 'UPDATE movies SET movie_file_path="'.$moviePath.'" WHERE movie_tmdbID="'.$movieID.'"';

    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','update_file_apth_alert');
        header('Refresh:0');
        exit();
    } else {
        set_callout('success','update_file_path_success');
        header('Refresh:0');
        exit();
    }
}

//-- Outputs a html player with selected movie as source --
function moviePlayer($movieID, $fullscreen = false) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT movie_file_path, movie_thumbnail FROM movies WHERE movie_tmdbID='".$movieID."'";
    $filePath = $conn->query($sql)->fetch_assoc()['movie_file_path'];

    if ( $filePath !== "" ) {
        if($fullscreen === true) {
            echo '<figure>';
                echo '<video id="player-'.$movieID.'" class="video-js" data-set="fullscreen" data-fullscreen="true" data-sound="true" controls preload="auto" data-setup="{}">'; //'.$tmdb->getImageURL().$backdrop.'
                    echo '<source src="'.$filePath.'" type="video/mp4" />';
                echo '</video>';
            echo '</figure>';
        } else {
            echo '<figure>';
                echo '<video id="player-'.$movieID.'" class="video-js" data-sound="true" data-fullscreen="true" controls preload="auto" data-setup="{}">'; //'.$tmdb->getImageURL().$backdrop.'
                    echo '<source src="'.$filePath.'" type="video/mp4" />';
                echo '</video>';
            echo '</figure>';
        }
    }
}

//-- Updates the previewd poster image of movies --
function updateMoviePoster($movieID, $poster) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $posterPATH = mysqli_real_escape_string($conn, $poster);

    $sql = 'UPDATE movies SET movie_poster="'.$posterPATH.'" WHERE movie_tmdbID="'.$movieID.'"';
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','update_poster_alert');
        header('Refresh:0');
        exit();
    } else {
        set_callout('success','update_poster_success');
        header('Refresh:0');
        exit();
    }
}

//-- Updates the previewd backdrop image of movies --
function updateMovieBackdrop($movieID, $backdrop) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $backdropPATH = mysqli_real_escape_string($conn, $backdrop);

    $sql = 'UPDATE movies SET movie_thumbnail="'.$backdropPATH.'" WHERE movie_tmdbID="'.$movieID.'"';
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','update_backdrop_alert');
        header('Refresh:0');
        exit();
    } else {
        set_callout('success','update_backdrop_success');
        header('Refresh:0');
        exit();
    }
}

//-- Outputs the release date as DD.MM.YYYY --
function outputDate($date) {
    $formattedDate = date("d.m.Y", strtotime($date));
    return $formattedDate;
}

//-- Outputs Runtime as HH hours MM minuts --
function runtimeToString($runtime) {
    $hours = floor($runtime / 60);
    $restMinutes = $runtime % 60;
    
    if (!($hours == 1)) {
        $minuteText = lang_snippet('minutes');
    } else {
        $minuteText = lang_snippet('minute');
    }

    if ($hours > 0 ) {
        if (!($hours > 1)) {
            $hourText = lang_snippet('hour');
        } else {
            $hourText = lang_snippet('hours');
        }

        $finalRuntime = $hours.' '.$hourText.' '. $restMinutes . ' '.$minuteText;
    } else {
        $finalRuntime = $restMinutes .' '.$minuteText;
    }

    return $finalRuntime;
}

//------------------------------------------------
//------------------------------------------------
//-- For Index (Home) Page -----------------------
function mainPageListMovies() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = "SELECT * FROM genres ORDER BY genre_id ASC";
    $results = $conn->query($sql);

    if ($results->num_rows > 0) {
        while ($rowGenres = $results->fetch_assoc()) {
            $movieRow = goTrhoughMovies($rowGenres);
            if ( $movieRow != '' ) {
                echo '<div class="row margin-bottom-l">';
                    echo '<div class="col12 column marg-bottom-l">';
                        echo '<div class="column">';
                            echo '<h3>'.$rowGenres['genre_name'].'</h3>';
                        echo '</div>';

                        echo '<div class="column">'; 
                            echo '<div class="swiper card-slider">';
                                echo '<div class="swiper-wrapper">';
                                    echo $movieRow;
                                echo '</div>';
                                echo '<div class="swiper-button-prev"></div>
                                <div class="swiper-button-next"></div>';
                            echo '</div>';
                        echo '</div>';
                    echo '</div>';
                echo '</div>';
            }
        }
    } else {
        echo '<p>Keine Genres vorhanden!</p>';
    }
}

function goTrhoughMovies($db_genre) {
    $tmdb = setupTMDB();
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $movieSQL = "SELECT * FROM movies";
    $resultsMovies = $conn->query($movieSQL);
    $movieRow = '';

    if ($resultsMovies->num_rows > 0) {
        while ($movie = $resultsMovies->fetch_assoc() ) {
            $movieID = $movie['movie_tmdbID'];
            $movieTitle = $movie['movie_title'];
            $movieOverview = $movie['movie_overview'];
            $movieRating = $movie['movie_rating'];
            $movieRuntime = $movie['movie_runtime'];
            $moviePoster = $movie['movie_poster'];
            $genres = json_decode($movie['movie_genres']);

            foreach ( $genres as $genre ) {
                if ( $db_genre['genre_id'] == $genre ) {
                    $movieRow = $movieRow . '
                    <div class="swiper-slide">
                        <a href="#info-popup-'.$movieID.'" title="'.$movieTitle.'" class="media-card" data-fancybox data-src="#info-popup-'.$movieID.'">
                            <figure class="poster">
                                <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">
                            </figure>
                            <span class="title">'.truncate($movieTitle,20).'</span>
                        </a>

                        <div class="info-popup" id="info-popup-'.$movieID.'" style="display:none;">
                            <div class="row">
                                <div class="col8">
                                    <p class="h4">'.$movieTitle.'</p>
                                    <p>'.$movieOverview.'</p>
                                    <div class="col6">
                                        <span><strong>Bewertung:</strong><br>'.$movieRating.'/10</span>
                                    </div>
                                    <div class="col6">
                                        <span><strong>Dauer:</strong><br>'.runtimeToString($movieRuntime).'</span>
                                    </div>
                                </div>
                                <div class="col4">
                                    <figure class="poster">
                                        <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">
                                    </figure>
                                </div>
                            </div>
                        </div>
                    </div>'; 
                }
            }
        }
    }

    return $movieRow;
}
//------------------------------------------------
//------------------------------------------------
//------------------------------------------------