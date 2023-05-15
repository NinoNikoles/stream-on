<?php
require_once ROOT_PATH.'/admin/language.php';

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

function truncate($string,$length=100,$append=" ...") {
    $string = trim($string);

    if(strlen($string) > $length) {
      $string = wordwrap($string, $length);
      $string = explode("\n", $string, 2);
      $string = $string[0] . $append;
    }
  
    return $string;
}

function insert_movie($conn, $tmdb, $movieID) {
	if(isset($_POST['add-movie'])) {
        $id = mysqli_real_escape_string($conn, $movieID);
        $movie = $tmdb->getMovie($id);
        $title =  mysqli_real_escape_string($conn, $movie->getTitle());
        $backdrop = mysqli_real_escape_string($conn, $movie->getBackdrop());
        $poster = mysqli_real_escape_string($conn, $movie->getPoster());;

        $sql = 'INSERT INTO movies (movie_tmdbID, movie_title, movie_poster, movie_thumbnail)
                VALUES ("'.$id.'", "'.$title.'", "'.$poster.'", "'.$backdrop.'")';
        if (!($conn->query($sql) === TRUE)) {
            set_callout('alert','addmoviealert');
            header('Location: /movies');
            exit();
        } else {
            set_callout('success','addmoviesuccess');
            header('Location: /movies');
            exit();
        }
    }
}

function selectMovieByID($movieID) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM movies WHERE movie_tmdbID='".$movieID."'";
    $result = $conn->query($sql);

    return $result;
}

function selectAllMoviesByTitle($order = ''){
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = "SELECT * FROM movies ORDER BY movie_title $order";
    $result = $conn->query($sql);

    return $result;
}

function updateMoviePoster($movieID, $poster) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $posterPATH = mysqli_real_escape_string($conn, $poster);

    $sql = 'UPDATE movies SET movie_poster="'.$posterPATH.'" WHERE movie_tmdbID="'.$movieID.'"';
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','update poster alert');
        header('Refresh:0');
        exit();
    } else {
        set_callout('success','update poster success');
        header('Refresh:0');
        exit();
    }
}

function updateMovieBackdrop($movieID, $backdrop) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $backdropPATH = mysqli_real_escape_string($conn, $backdrop);

    $sql = 'UPDATE movies SET movie_thumbnail="'.$backdropPATH.'" WHERE movie_tmdbID="'.$movieID.'"';
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','update backdrop alert');
        header('Refresh:0');
        exit();
    } else {
        set_callout('success','update backdrop success');
        header('Refresh:0');
        exit();
    }
}

function runtimeToString($runtime) {
    $hours = floor($runtime / 60);
    $restMinutes = $runtime % 60;
    
    if (!($hours == 1)) {
        $minuteText = lang_snippet('Minutes');
    } else {
        $minuteText = lang_snippet('Minute');
    }

    if ($hours > 0 ) {
        if (!($hours > 1)) {
            $hourText = lang_snippet('Hour');
        } else {
            $hourText = lang_snippet('Hours');
        }

        $finalRuntime = $hours.' '.$hourText.' '. $restMinutes . ' '.$minuteText;
    } else {
        $finalRuntime = $restMinutes .' '.$minuteText;
    }

    return $finalRuntime;
}