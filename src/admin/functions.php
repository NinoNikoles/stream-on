<?php
require_once ROOT_PATH.'/src/admin/language.php';

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Init --///////////

//-- DB connection --
function dbConnect() {
    return new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
}

//-- Returns TMDB Class --
function setupTMDB() {
    include ROOT_PATH.'/src/tmdb/configuration/default.php';
    require_once ROOT_PATH.'/src/tmdb/tmdb-api.php';
    $tmdb = new TMDB($cnf);

    return $tmdb;
}

function tmdbConfig() {
    include ROOT_PATH.'/src/tmdb/configuration/default.php';
    return $cnf;
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Querys --///////////

function insert($table, $data, $values) {
    $values = json_encode($values);
    $replace = array('[', ']');
    $values = str_replace($replace, '', $values);
    echo "INSERT INTO $table ($data) VALUES ($values)";
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Redirect --///////////

//-- Redirect --
function page_redirect($location) {
    echo '<script>window.location.href = "'.$location.'";</script>';
    exit();
}

function page_refresh() {
    echo '<script>window.location.reload();</script>';
    exit();
}

function checkIfUserExists($username) {
    $conn = dbConnect();
    $result = $conn->query("SELECT username FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}


////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Page infos --///////////

function getSiteTitle() {
    $conn = dbConnect();

    $sql = "SELECT * FROM settings WHERE setting_name='site_title'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['setting_option'];
        }
    }
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
		setcookie($themeCookie, 'dark', $expireDate, '/');
		$theme = 'data-theme="dark"';
	} else if ( $theme === 'dark' ) {
		setcookie($themeCookie, 'dark', $expireDate, '/');
		$theme = 'data-theme="dark"';
	} else if ( $theme !== 'dark' ) {
		setcookie($themeCookie, $theme, $expireDate, '/');
		$theme = 'data-theme="'.$theme.'"';
	}

	return $theme;
}

function setFavicon($PATH) {
    $conn = dbConnect();
    $sql = "UPDATE settings SET movie_file_path='$PATH' WHERE setting_name='favicon_path'";

    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','update_file_apth_alert');
        page_refresh();
    } else {
        set_callout('success','update_file_path_success');
        page_refresh();
    }
};

function get_apikey_db() {
    $conn = dbConnect();

    $sql = "SELECT setting_option FROM settings WHERE setting_name='apikey'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['setting_option'];
        }
    }
}

/*function faviconPath() {
    $bildName = $_FILES['user-img']['name'];
    $bildTmpName = $_FILES['user-img']['tmp_name'];
    $bildSize = $_FILES['user-img']['size'];
    $bildError = $_FILES['user-img']['error'];
    $bildType = $_FILES['user-img']['type'];

    // Dateiendung des Bildes extrahieren
    $bildExt = strtolower(pathinfo($bildName, PATHINFO_EXTENSION));

    // Erlaubte Dateitypen festlegen
    $erlaubteTypen = array('jpg', 'jpeg', 'png', 'gif', 'svg');

    // Überprüfen, ob die Datei ein Bild ist und erlaubte Dateitypen hat
    if(in_array($bildExt, $erlaubteTypen)) {
        // Dateinamen für das Bild generieren
        $neuerName = uniqid('', true) . '.' . $bildExt;
        $ziel = ROOT_PATH.'/uploads/' . $neuerName;

        // Bild in den Zielordner verschieben
        move_uploaded_file($bildTmpName, $ziel);

        $sql = 'UPDATE users SET user_img="'.$neuerName.'" WHERE id="'.$_POST['id'].'"';
        if ($conn->query($sql) === TRUE) {
            echo "Record updated successfully";
            set_callout('success','user_img_upload_success');
            header('Location: /user/?id='.$_POST['id']);
            exit();
        } else {
            set_callout('alert','user_img_upload_alert');
            header('Location: /user/?id='.$_POST['id']);
            exit();
        }
    } else {
        echo 'Das hochgeladene File muss ein Bild sein (JPG, JPEG, PNG, GIF).';
        set_callout('warning','user_img_upload_wrong_file');
        header('Location: /user/?id='.$_POST['id']);
        exit();
    }

    $conn = dbConnect();

    $sql = "SELECT setting_option FROM settings WHERE setting_name='favicon_path'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            return $row['setting_option'];
        }
    }
};*/

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Callout --///////////

function set_callout($type, $message) {
    $_SESSION['callout_type'] = $type;
    $_SESSION['callout_message'] = $message;
}

function callout() {
    if ( isset($_SESSION['callout_type']) && isset($_SESSION['callout_message']) ) {
        if ( $_SESSION['callout_type'] != '' && $_SESSION['callout_message'] != '' ) {
            $type = $_SESSION['callout_type'];
            $message = lang_snippet($_SESSION['callout_message']);
    
            echo '<div class="callout '.$type.' mag-bottom-l">';
                echo '<p>'.$message.'</p>';
            echo '</div>';
    
            $_SESSION['callout_type'] = '';
            $_SESSION['callout_message'] = '';
        }
    }
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Menu --///////////

function adminMenu() {
    if ($_SESSION['role'] == '1') {
        return '<li class="menu-item"><a href="/admin/settings" title="'.lang_snippet('settings').'">'.lang_snippet('settings').'</a></li>';
    }
}

//-- Loads the backend menu --
function get_backend_menu() {
	return include(ROOT_PATH.'/views/includes/backend-menu.php');
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Universal --///////////

function truncate($string,$length=100,$append=" ...") {
    $string = trim($string);

    if(strlen($string) > $length) {
      $string = wordwrap($string, $length);
      $string = explode("\n", $string, 2);
      $string = $string[0] . $append;
    }
  
    return $string;
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Settings --///////////

function updateSettings($values) {
    $conn = dbConnect();

    $siteTitle = mysqli_real_escape_string($conn, $values['site_title']);
    $apikey = mysqli_real_escape_string($conn, $values['apikey']);
    $apiLang = mysqli_real_escape_string($conn, $values['language']);

    $sql = "INSERT INTO settings(setting_name, setting_option) VALUES 
    ('site_title', '$siteTitle'),
    ('apikey', '$apikey'),
    ('apilang', '$apiLang')
    ON DUPLICATE KEY UPDATE setting_option = VALUES(setting_option)";

    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','settings_update_failed');
        page_redirect('/admin/settings');
    } else {
        set_callout('success','settings_update_success');
        page_redirect('/admin/settings');
    }
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Movie --///////////

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

    $sql = "SELECT * FROM movies WHERE movie_tmdbID='$movieID'";
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

//-- Check if movie is in local database --
function movieInLocalDB($movieID) {
    $conn = dbConnect();

    $sql = "SELECT movie_tmdbID FROM movies WHERE movie_tmdbID='$movieID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        return true;
    }
}

//-- Returns all local database movies ordered by A-Z or Z-A --
function selectAllMoviesByTitle($order = ''){
    $tmdb = setupTMDB();
    $conn = dbConnect();

    if ( $order != '' ) {
        $sql = "SELECT * FROM movies ORDER BY movie_title $order";
    } else {
        $sql = "SELECT * FROM movies";
    }
    
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

//-- Returns all local database movies ordered by A-Z or Z-A --
function selectMovieByTitle($title){
    $tmdb = setupTMDB();
    $conn = dbConnect();
    if ( $title !== '' ) {
        $sql = "SELECT * FROM movies WHERE movie_title LIKE '%$title%'";
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
    } else {
        return $data = '';
    }
}

//-- Checks if the movie is already in local database so it wont show up in movie collections --
function movieIsInCollection($id){
    $conn = dbConnect();

    $sql = "SELECT * FROM movies WHERE movie_tmdbID='$id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        return true;
    }
}

//-- Updates the filepath of movie sources --
function updateMovieFilePath($moviePath, $movieID) {
    $conn = dbConnect();
    $moviePath = mysqli_real_escape_string($conn, $moviePath);

    $sql = "UPDATE movies SET movie_file_path='$moviePath' WHERE movie_tmdbID='$movieID'";

    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','update_file_apth_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
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
        set_callout('alert','update_poster_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
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
        set_callout('alert','update_backdrop_alert');
        page_redirect('/admin/movie/?id='.$movieID);
    } else {
        set_callout('success','update_backdrop_success');
        page_redirect('/admin/movie/?id='.$movieID);
    }
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Video player --///////////
function videoPlayer($movieID, $fullscreen = false) {
    $conn = dbConnect();
    $sql = "SELECT id, movie_file_path, movie_thumbnail FROM movies WHERE movie_tmdbID='$movieID'";
    $filePath = $conn->query($sql)->fetch_assoc()['movie_file_path'];
    $id = $conn->query($sql)->fetch_assoc()['id'];

    if ( $filePath !== "" ) {
        $userID = $_SESSION['userID'];
        $sqlTime = "SELECT watched_seconds FROM movie_watched WHERE user_id='$userID ' and movie_id='$movieID'";
        $watchedTime = $conn->query($sqlTime)->fetch_assoc()['watched_seconds'];

        if($fullscreen === true) {
            echo '<figure>';
                echo '<video id="player" class="video-js" data-id="'.$movieID.'" data-set="fullscreen" data-fullscreen="true" data-sound="true" controls preload="auto" data-volume-panel="vertical">'; //'.$tmdb->getImageURL().$backdrop.' //
                    echo '<source src="'.$filePath.'" type="video/mp4"/>';
                echo '</video>';
            echo '</figure>';
        } else {
            echo '<figure class="widescreen">';
                echo '<video id="player" class="video-js" data-id="'.$movieID.'" data-sound="true" data-fullscreen="true" controls preload="auto">'; //'.$tmdb->getImageURL().$backdrop.'
                    echo '<source src="'.$filePath.'" type="video/mp4" />';
                echo '</video>';
            echo '</figure>';
        }
        echo '<span data-time="'.$watchedTime.'"></span>';
    }
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Time & Date --///////////

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

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Genre --///////////

function initGenres() {
    $conn = dbConnect();
    $tmdb = setupTMDB();

    $genres = $tmdb->getGenres();
    $data = [];
    foreach ($genres as $genre) {
        $data[] = '("'.mysqli_real_escape_string($conn, $genre->getID()).'", "'.mysqli_real_escape_string($conn, $genre->getName()).'")';
    }

    $dataSring = json_encode($data);
    $dataSring = str_replace(array('[', ']', "[", "]"), '', $dataSring);
    $dataSring = str_replace('"(', '(', $dataSring);
    $dataSring = str_replace(')"', ')', $dataSring);
    $dataSring = stripslashes($dataSring);

    $sql = "INSERT INTO genres (genre_id, genre_name) VALUES $dataSring";
    $result = $conn->query($sql);

    if (!$result) {
        set_callout('alert','genres_created_alert');
        page_redirect("/admin/genres");
    } else {
        set_callout('success','genres_created_success');
        page_redirect("/admin/genres");
    }
}

function genreCheck() {
    $conn = dbConnect();
    $sql = "SELECT id FROM genres";
    $results = $conn->query($sql);
    
    if ($results->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}

function getDBGenreNameByID($id) {
    $conn = dbConnect();
    $sql = "SELECT * FROM genres WHERE genre_id='$id'";
    $results = $conn->query($sql);
    if ($results->num_rows > 0) {
        while ($genre = $results->fetch_assoc()) {
            if ( isset($genre['genre_name']) ) {
                return $genre['genre_name'];
            }
        }
    }
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- User --///////////

function getUserID() {
    return $_SESSION['userID'];
}

function userCheck() {
    $conn = dbConnect();
    $sessionUserID = $_SESSION['userID'];
    $sql = "SELECT id, username, user_img FROM users WHERE id='$sessionUserID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      
    // output data of each row
        while($row = $result->fetch_assoc()) {
            if ( $_GET['id'] !== intval($_SESSION['userID']) || $_GET['id'] !== $row['id'] || $row['username'] != $_SESSION['username'] ) {
                page_redirect("/login");
            }
        }
    } else {
        page_redirect("/login");
    }
}

function userProfileImg() {
    $conn = dbConnect();
    $sessionUserID = $_SESSION['userID'];
    $sql = "SELECT user_img FROM users WHERE id='$sessionUserID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
     
        // output data of each row
        while($row = $result->fetch_assoc()) {
            
            if ($row['user_img'] === NULL || $row['user_img'] === '') {
                $userProfileImg = '/views/build/css/images/placeholder.webp';
            } else {
                $userProfileImg = '/uploads/'.$row['user_img'];
            }
        }
    } else {
        $userProfileImg = '/views/build/css/images/placeholder.webp';
    }

    return $userProfileImg;
}

function registerUser($post) {
    $conn = dbConnect();
    $username = mysqli_real_escape_string($conn, $post['username']);
    if (isset($post['role']) && $_POST['role'] === 'on') {
        $role = mysqli_real_escape_string($conn, 1);
    } else {
        $role = mysqli_real_escape_string($conn, 0);;
    }
    $password = mysqli_real_escape_string($conn, $post['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, role, password) VALUES ('$username', '$role', '$hashed_password')";
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','add_user_alert');
        page_redirect("/admin/users");
    } else {
        set_callout('success','add_user_success');
        page_redirect("/admin/users");
    }
}

function editUser($post) {
    $conn = dbConnect();
    $userID = mysqli_real_escape_string($conn, $post['userID']);
    $username = mysqli_real_escape_string($conn, $post['username']);
    if (isset($post['role'])) {
        $role = mysqli_real_escape_string($conn, $post['role']);
    } else {
        $role = 0;
    }
    $password = mysqli_real_escape_string($conn, $post['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET username='$username', password='$hashed_password', role='$role' WHERE id='$userID'";

    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','delete_user_alert');
        page_redirect("/admin/users");
    } else {
        set_callout('success','edit_user_success');
        session_start();
        $_SESSION['username'] = $username;
        $_SESSION['role'] = $role;
        $_SESSION['logged_in'] = true;

        page_redirect('/admin/users');
    }
}

function deleteUser($userID) {
    $conn = dbConnect();
    $sql = "DELETE FROM users WHERE id='$userID'";
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','delete_user_alert');
        page_redirect("/admin/users");
    } else {
        set_callout('success','delete_user_success');
        page_redirect("/admin/users");
    }
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- API --///////////


function scrollLoader($media, $count) {
    $conn = dbConnect();

    $sql = "SELECT * FROM $media ORDER BY movie_title ASC LIMIT 20 OFFSET $count";
    $results = $conn->query($sql);

    $data = [];
    $i = 0;

    if ($results->num_rows > 0) {
        while ($row = $results->fetch_assoc()) {

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
            $data[$i]['genres'] = $row['movie_genres'];

            $genres = json_decode($data[$i]['genres']);

            $dataGenres = [];
            foreach ( $genres as $genre ) {
                $dataGenres[] = array(
                    'id' => $genre,
                    'name' => getDBGenreNameByID($genre)
                );
            }

            $data[$i]['genres'] = $dataGenres;

            $i++;
        }
    }

    return $data;
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Watch list --///////////

function currentWatchlist() {
    $conn = dbConnect();
    $tmdb = setupTMDB();
    $userID = $_SESSION['userID'];

    $query = "SELECT * FROM movies INNER JOIN movie_watched ON movies.movie_tmdbID = movie_watched.movie_id WHERE movie_watched.user_id = $userID and movie_watched.watched_seconds > 0";
    $results = $conn->query($query);
    
    if ( $results->num_rows > 0 ) {
        echo '<div class="currentWatch-slider">';
            echo '<div class="col12 marg-top-l">';
                echo '<div class="column">';
                    echo '<h3>'.lang_snippet('continue').'</h3>';
                echo '</div>';

                echo '<div class="column">'; 
                    echo '<div class="swiper card-slider">';
                        echo '<div class="swiper-wrapper">';
                            
                        while ( $movie = $results->fetch_assoc() ) {
                            $movieID = $movie['movie_tmdbID'];
                            $watched = intval($movie['watched']);

                            if ( $watched !== 1 ) {                                
                                $movieTitle = $movie['movie_title'];
                                $movieOverview = $movie['movie_overview'];
                                $movieRating = $movie['movie_rating'];
                                $movieRuntime = $movie['movie_runtime'];
                                $movieRelease = new DateTime($movie['movie_release']);
                                $releaseYear = $movieRelease->format('Y');
                                $moviePoster = $movie['movie_poster'];
                                $movieBackdrop = $movie['movie_thumbnail'];
                                $genres = json_decode($movie['movie_genres']);
                                $genreHTML = '';
                                foreach ( $genres as $genre ) {
                                    $genreHTML = $genreHTML . '<span class="tag">'.getDBGenreNameByID($genre).'</span>';
                                }
                                $watchedTime = floatval($movie['watched_seconds']);
                                $totalDuration = floatval($movie['total_length']);
                                $watchedInPercent = ($watchedTime/$totalDuration)*100;
    
                                echo    '<div class="swiper-slide">
                                            <div class="desktop-only">
                                                <div class="media-card widescreen-media-card">
                                                    <figure class="widescreen">
                                                        <img src="'.$tmdb->getImageURL().$movieBackdrop.'" alt="">
                                                    </figure>
                                                    <div class="link-wrapper">
                                                        <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                                                        <a href="#content-'.$movieID.'" title="'.lang_snippet('more_informations').'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                                                    </div>
                                                </div>
                                                <div class="watched-bar">
                                                        <progress max="100" value="'.$watchedInPercent.'"></progress>
                                                    </div>
                                                </div>
                                            <div>

                                            <div class="mobile-only">
                                                <div class="media-card">
                                                    <figure class="poster">
                                                        <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">
                                                    </figure>
                                                    <div class="link-wrapper">
                                                        <a href="/watch/?id='.$movieID.'" title="'.$movieTitle.'" class="play-trigger"></a>
                                                        <a href="#content-'.$movieID.'" title="'.$movieTitle.'" class="info-trigger" data-modal data-src="#content-'.$movieID.'"></a>
                                                    </div>
                                                </div>
                                                <div class="watched-bar">
                                                    <progress max="100" value="'.$watchedInPercent.'"></progress>
                                                </div>
                                            </div>
            
                                            <div class="info-popup" id="content-'.$movieID.'" style="display:none;">
                                                <div class="col12 marg-bottom-xs mobile-only">
                                                    <figure class="widescreen">
                                                        <img src="'.$tmdb->getImageURL().$movieBackdrop.'">
                                                    </figure>
                                                </div>
                                                <div class="innerWrap">
                                                    <div class="col7 marg-right-col1">
                                                        <p class="h2">'.$movieTitle.'</p>
                                                        <p class="small">
                                                            <span class="tag">'.$releaseYear.'</span>
                                                            <span class="tag">'.$movieRating.'/10</span>
                                                            <span class="tag">'.runtimeToString($movieRuntime).'</span>
                                                        </p>
                                                        <a href="/watch/?id='.$movieID.'" class="btn btn-white icon-left icon-play">Jetzt schauen</a>
                                                        <p class="small">'.$movieOverview.'</p>
                                                        <p class="small">'.$genreHTML.'</p>
                                                    </div>
                                                    <div class="col4 desktop-only">
                                                        <figure class="poster">
                                                            <img src="'.$tmdb->getImageURL().$moviePoster.'" alt="">
                                                        </figure>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                            } else {
                                $updateWatched = "UPDATE movie_watched SET watched='1' WHERE user_id='$userID' and movie_id='$movieID'";
                                $conn->query($updateWatched);
                            }
                        }
                        echo '</div>';
                        echo '<div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>';
                    echo '</div>';
                echo '</div>';
            echo '</div>';
        echo '</div>';
    }
}