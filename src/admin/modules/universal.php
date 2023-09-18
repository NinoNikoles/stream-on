<?php
////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Init --///////////

//-- DB connection --
function dbConnect() {
    if ( file_exists( ROOT_PATH.'/config.php') ) {
        if ( errorCatch('ignore') ) {
            $servername = DB_HOST;
            $username = DB_USER;
            $password = DB_PASSWORD;
            $dbname = DB_NAME;
    
            return new mysqli($servername, $username, $password, $dbname);  
        } else {
            $servername = "localhost";
            $username = "root";
            $password = "";

            return new mysqli($servername, $username, $password);
        }
      
    } else {
        $servername = "localhost";
        $username = "root";
        $password = "";

        return new mysqli($servername, $username, $password);
    }    
}

function tmdbConfig() {
    include ROOT_PATH.'/src/tmdb/configuration/default.php';
    return $cnf;
}

//-- Returns TMDB Class --
function setupTMDB() {
    $cnf = tmdbConfig();
    require_once ROOT_PATH.'/src/tmdb/tmdb-api.php';
    $tmdb = new TMDB($cnf);

    return $tmdb;
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Querys --///////////

function insert($table, $data, $values, $conditon = "") {
    $values = json_encode($values);
    $replace = array('[', ']');
    $values = str_replace($replace, '', $values);
    echo "INSERT INTO $table ($data) VALUES ($values) ".$conditon;
}

function update($table, $data, $conditon = "") {
    echo "UPDATE $table SET $data WHERE ".$conditon;
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

function ajaxPage_redirect($location) {
    $redirect = array(
        "type" => 'redirect',
        "location" => $location
    );

    $redirect = json_encode($redirect);

    echo $redirect;
}

function checkIfUserExists($username) {
    $conn = dbConnect();
    $result = $conn->query("SELECT username FROM users WHERE username='$username'");
    if ($result->num_rows > 0) {
        $conn->close();
        return true;
    } else {
        $conn->close();
        return false;
    }
}

function destroySesssion() {
    session_unset();
    session_destroy();
    session_write_close();
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Page infos --///////////

function pageTitle($page) {
    $conn = dbConnect();
    $title = [];
    $title[0] = 'Install';
    $title[1] = '';

    if ( !($page === '') ) {
        $page = $page . ' - ';
    }

    if ( file_exists( ROOT_PATH.'/config.php') ) {
        if ( databaseExists(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) ) {
            if ( tablesExists(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) ) {
                $query = "SHOW TABLES LIKE 'settings'";
                $result = $conn->query($query);
    
                if ($result->num_rows > 0) {
                    $sql = "SELECT setting_option FROM settings WHERE setting_name='site_title'";
                    if ( !($conn->connect_error) ) {
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                $conn->close();
                                
                                $title[0] = $page;
                                $title[1] = $row['setting_option'];
                                return $title;
                            }
                        }
                    } 
                }
            } 
        }
    }

    return $title;
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
        $conn->close();
        set_callout('alert','update_file_path_alert');
        page_refresh();
    } else {
        $conn->close();
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
            $conn->close();
            return $row['setting_option'];
        }
    } else {
        return false;
    }
}

function loadFavicon() {
    $iconPath = '/views/assets/icons';
    favicon($iconPath);
};

function favicon($iconPath) {
    echo '<link rel="apple-touch-icon" sizes="180x180" href="'.$iconPath.'/apple-touch-icon.png">';
    echo '<link rel="icon" type="image/png" sizes="32x32" href="'.$iconPath.'/favicon-32x32.png">';
    echo '<link rel="icon" type="image/png" sizes="16x16" href="'.$iconPath.'/favicon-16x16.png">';
    echo '<link rel="manifest" href="'.$iconPath.'/site.webmanifest">';
    echo '<link rel="mask-icon" href="'.$iconPath.'/safari-pinned-tab.svg" color="#5bbad5">';
    echo '<meta name="msapplication-TileColor" content="#da532c">';
    echo '<meta name="theme-color" content="#ffffff">';
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Callout --///////////

function set_callout($type, $message) {
    $callout = array(
        "type" => $type,
        "msg" => $message
    );

    $callout = json_encode($callout);

    echo $callout;
}

function callout() {
    echo '<div id="callout" class="callout mag-bottom-l">';
    echo '</div>';
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Menu --///////////

function adminMenu($menu) {
    $returnMenu = '';
    if ( $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'superadmin' ) {
        if ( $menu === 'main-menu' ) {
            $returnMenu .= '
            <li class="menu-item"><a href="/admin/settings" title="'.lang_snippet('settings').'">'.lang_snippet('settings').'</a></li>
            <li class="menu-item mobile-only"><a href="/admin/users" title="'.lang_snippet('users').'">'.lang_snippet('users').'</a></li>';
            
            $apikey = get_apikey_db();
            var_dump($apikey);
            if ( !($apikey === NULL) ) {
                $returnMenu .= '<li class="menu-item mobile-only"><a href="/admin/genres" title="'.lang_snippet('genres').'">'.lang_snippet('genres').'</a></li>';
            
                $genreCheck = genreCheck();
                if ( $genreCheck === true) {
                    $returnMenu .= '
                    <li class="menu-item mobile-only"><a href="/admin/movies" title="'.lang_snippet('movies').'">'.lang_snippet('movies').'</a></li>
                    <li class="menu-item mobile-only"><a href="/admin/shows" title="'.lang_snippet('shows').'">'.lang_snippet('shows').'</a></li>
                    <li class="menu-item mobile-only"><a href="/admin/highlights" title="'.lang_snippet('highlights').'">'.lang_snippet('highlights').'</a></li>';
                }
            }            
        } else if ( 'user-menu' ) {
            $returnMenu .=  '<li class="menu-item desktop-only"><a href="/admin/settings" title="'.lang_snippet('settings').'">'.lang_snippet('settings').'</a></li>';
        }
    }

    return $returnMenu;
}

//-- Loads the backend menu --
function get_backend_menu() {
	return include(ROOT_PATH.'/views/includes/backend-menu.php');
}

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Universal --///////////

//--
function include_modules($file) {
    require_once ROOT_PATH.'/src/admin/modules/'.$file.'.php';
}

function truncate($string,$length=100,$append="...") {

    if(strlen($string) > $length) {
        $string = substr($string, 0, $length) . $append;
    }
  
    return $string;
}

function text_truncate($text, $laenge) {
        if (strlen($text) > $laenge) {
            $text = substr($text, 0, $laenge); // Zuerst den Text auf die gewünschte Länge kürzen
            $text = substr($text, 0, strrpos($text, ' ')); // Dann den Text auf das letzte Leerzeichen zurückkürzen
            $text .= ' ...'; // Am Ende " [...]" hinzufügen
        }
        return $text;
    }

////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////
//////////-- Settings --///////////

function updateSettings($values) {
    $conn = dbConnect();

    $siteTitle = mysqli_real_escape_string($conn, $values['site_title']);
    $apikey = mysqli_real_escape_string($conn, $values['apikey']);
    $apiLang = mysqli_real_escape_string($conn, $values['language']);
    $checked = "false";
    
    if ( $values['enable_edit'] === 'true' ) {
        $checked = "checked";
    }

    $sql = "INSERT INTO settings(setting_name, setting_option) VALUES 
    ('site_title', '$siteTitle'),
    ('apikey', '$apikey'),
    ('apilang', '$apiLang'),
    ('enable_edit_btn', '$checked')
    ON DUPLICATE KEY UPDATE setting_option = VALUES(setting_option)";

    if (!($conn->query($sql) === TRUE)) {
        $conn->close();
        set_callout('alert',lang_snippet('settings_update_failed'));
    } else {
        $conn->close();
        set_callout('success',lang_snippet('settings_update_success'));
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
    $restMinutes = intval($runtime % 60);
    
    if (!($restMinutes <= 1)) {
        $minuteText = lang_snippet('minutes');
    } else if ( $restMinutes < 1 ) {
        $restMinutes = '';
        $minuteText = '';
    } else {
        $minuteText = lang_snippet('minute');
    }

    if ($hours > 0 ) {
        if ( $hours <= 1 ) {
            $hourText = lang_snippet('hour');
        } else {
            $hourText = lang_snippet('hours');
        }

        $finalRuntime = $hours.' '.$hourText.' '.$restMinutes. ' '.$minuteText;
    } else {
        $finalRuntime = $restMinutes .' '.$minuteText;
    }
 
    return $finalRuntime;
}

//-- TMDB IMG Path --
function loadImg($size, $img) {
    //return "http://image.tmdb.org/t/p/$size$img";
    return '/views/build/css/images/img_preview.webp';
}

//-- User Volume --
function getVolume($userID) {
    $conn = dbConnect();
    $sql = "SELECT media_volume FROM users WHERE id = $userID;";

    $volume = $conn->query($sql)->fetch_assoc()['media_volume'];
    if ( $volume === NULL ) {
        $volume = 1;
    }
    
    return $volume;
}

function getUUID() {
    if (function_exists('random_bytes')) {
        $data = random_bytes(16);
    } elseif (function_exists('openssl_random_pseudo_bytes')) {
        $data = openssl_random_pseudo_bytes(16);
    } else {
        $data = uniqid(mt_rand(), true);
    }

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4)); 
}

function userNameStringFormatter($username) {
    // String in Kleinbuchstaben umwandeln
    $formattedString = strtolower($username);
    
    // Sonderzeichen durch "-" ersetzen (außer Leerzeichen, die durch "_" ersetzt werden)
    $formattedString = preg_replace('/[^a-z0-9\s]/', '-', $formattedString);
    
    // Leerzeichen durch "_" ersetzen
    $formattedString = str_replace(' ', '_', $formattedString);
    
    return $formattedString;
}
?>