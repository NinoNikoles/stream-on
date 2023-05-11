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

function insert_movie($conn, $tmdb, $movieID) {
	if(isset($_POST['add-movie'])) {
        $id = mysqli_real_escape_string($conn, $movieID);
        $movie = $tmdb->getMovie($id);

        $title = mysqli_real_escape_string($conn, $movie->getTitle());
        $backdrop = mysqli_real_escape_string($conn, $movie->getBackdrop());
        $poster = mysqli_real_escape_string($conn, $movie->getPoster());       
        $tagline =  mysqli_real_escape_string($conn, $movie->getTagline());
        $overview =  mysqli_real_escape_string($conn, $movie->getOverview());
        $genres = [];
        $results = $movie->getGenres();
        foreach ($results as $genre) {
            $genres[] = $genre->getName();
        }
        $genres =  mysqli_real_escape_string($conn, implode(', ', $genres));
        $rating =  mysqli_real_escape_string($conn, $movie->getVoteAverage());
        $runtime = $movie->getRuntime();
        $release =  mysqli_real_escape_string($conn, $movie->getReleaseDate());
        $collection = $movie->getCollection();

        $sql = 'INSERT INTO movies (movie_tmdbID, movie_name, movie_tagline, movie_description, movie_genres, movie_poster, movie_thumbnail, movie_collection, movie_length, movie_release, movie_rating)
                VALUES ("'.$id.'", "'.$title.'", "'.$tagline.'", "'.$overview.'", "'.$genres.'", "'.$poster.'", "'.$backdrop.'", "'.$collection.'", "'.$runtime.'", "'.$release.'", "'.$rating.'")';
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