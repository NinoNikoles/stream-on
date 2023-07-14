<?php 

function configCheck() {
    if ( file_exists( ROOT_PATH.'/config.php') ) {
        require_once ROOT_PATH.'/config.php';
    }
}

function init() {
    if ( file_exists( ROOT_PATH.'/config.php') ) {
        if ( onetimesetup(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) ) {
            require_once ROOT_PATH.'/src/routes/routes.php';
        }
        else if ( !databaseExists(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) ) {
            get('/install', 'install.php');
            post('/install', 'install.php');
        
            if ( !pageCheck("/install") ) {
                page_redirect("/install");
            }
            
        } else if ( !tablesExists(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME) ) {
            get('/install', 'install.php');
            post('/install', 'install.php');
    
            if ( !pageCheck("/install") ) {
                page_redirect("/install");
            }
        } else {
            require_once ROOT_PATH.'/src/routes/routes.php';
        }
    } else {
        get('/install', 'install.php');
        post('/install', 'install.php');
    
        if ( !pageCheck("/install") ) {
            page_redirect("/install");
        }
    }
}

function onetimesetup($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        return false;
    }

    $result = $conn->query("SELECT setting_option FROM settings WHERE setting_name='one_time_setup'");
    
    $conn->close();
    return $result;
}

// Überprüfen, ob die erforderlichen Tabellen existieren
function databaseExists($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password);
    if ($conn->connect_error) {
        return false;
    }

    $result = $conn->query("Show DATABASES LIKE '".$dbname."'");
    $dbExists = $result->num_rows > 0;
    
    $conn->close();
    return $dbExists;
}

// Überprüfen, ob die erforderlichen Tabellen existieren
function tablesExists($servername, $username, $password, $dbname) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        return false;
    }
    
    $result = $conn->query("SHOW TABLES LIKE 'settings'");
    $tableExists = $result->num_rows > 0;
    
    $conn->close();
    return $tableExists;
}

function pageCheck($searchString) {
    // Überprüfe die REQUEST_URI
    if (strpos($_SERVER['REQUEST_URI'], $searchString) !== false) {
        return true;
    } else {
        return false;
    }
}

function createConfig($servername, $username, $password, $dbname) {
    $fileContent = "<?php
    /**
     * Ersetze localhost mit der MySQL-Serveradresse.
     */
    define( 'DB_HOST', '".$servername."' );
    
    /**
     * Ersetze benutzername_hier_einfuegen
     * mit deinem MySQL-Datenbank-Benutzernamen.
     */
    define( 'DB_USER', '".$username."' );
    
    /**
     * Ersetze passwort_hier_einfuegen mit deinem MySQL-Passwort.
     */
    define( 'DB_PASSWORD', '".$password."' );
    
    /**
     * Ersetze datenbankname_hier_einfuegen
     * mit dem Namen der Datenbank, die du verwenden möchtest.
     */
    define( 'DB_NAME', '".$dbname."' );
    
    /**
     * Der Datenbankzeichensatz, der beim Erstellen der
     * Datenbanktabellen verwendet werden soll
     */
    define( 'DB_CHARSET', 'utf8mb4' );
    
    /**
     * Der Collate-Type sollte nicht geändert werden.
     */
    define('DB_COLLATE', '');
    
    
    ?>";
    
    // Dateiname und Pfad für die neue Datei
    $filename = fopen('config.php', "w") or die("Unable to open file!");

    fwrite($filename, $fileContent);
    fclose($filename);
}

function createDatabase($servername, $username, $password, $dbname) {
    if (!databaseExists($servername, $username, $password, $dbname)) {
        $conn = new mysqli($servername, $username, $password);
    
        $sql = "CREATE DATABASE $dbname";
        if ( $conn->query($sql) === true ) {
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ( !tablesExists($servername, $username, $password, $dbname) === true ) {
                page_redirect("/install");
            } else {
                page_redirect("/");
            }
            
        } else {
            echo "Fehler beim Erstellen der Datenbank: " . $conn->error;
        }
    
        $conn->close();
    } else {
        if ( !tablesExists($servername, $username, $password, $dbname) === true ) {
            page_redirect("/install");
        } else {
            page_redirect("/");
        }
    }
}

function createTables($pageTitle, $adminUsername, $adminPassword, $apikey, $pageLang) {
    $conn = dbConnect();

    //-- Settings Table --
    $sql = "CREATE TABLE IF NOT EXISTS settings (
        id INT NOT NULL AUTO_INCREMENT,
        setting_name varchar(255),
        setting_option varchar(255),
        UNIQUE (setting_name),
        PRIMARY KEY (id)
    )";
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    } else {
        $sql = "INSERT INTO settings(setting_name, setting_option) VALUES 
        ('one_time_setup', 0),
        ('apikey', NULL),
        ('apilang', NULL),
        ('site_title', '$pageTitle')";
        if (!($conn->query($sql) === TRUE)) {
            die('Error creating table: ' . $conn->error);
        }
    }
    
    //-- User Table --
    $sql = 'CREATE TABLE IF NOT EXISTS users (
        id INT NOT NULL AUTO_INCREMENT,
        username varchar(255),
        firstname varchar(255),
        lastname varchar(255),
        password varchar(255),
        user_img varchar(255),
        role BOOLEAN,
        created TIMESTAMP,
        UNIQUE (username),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    } else {
        $username = $adminUsername;
        $password = $adminPassword;
        $role = 1;
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";
        if (!($conn->query($sql) === TRUE)) {
            die('Error creating table: ' . $conn->error);
        }
    }
    
    //-- Genre Table --
    $sql = 'CREATE TABLE IF NOT EXISTS genres (
        id INT NOT NULL AUTO_INCREMENT,
        genre_id INT NOT NULL,
        genre_name VARCHAR(255) NOT NULL,
        created TIMESTAMP,
        UNIQUE (genre_id),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating showsGenre: ' . $conn->error);
    }
    
    //-- Movie Table --
    $sql = 'CREATE TABLE IF NOT EXISTS movies (
        id INT NOT NULL AUTO_INCREMENT,
        movie_tmdbID INT,
        movie_title TEXT NOT NULL,
        movie_tagline TEXT NOT NULL,
        movie_overview TEXT NOT NULL,
        movie_poster TEXT NOT NULL,
        movie_thumbnail TEXT NOT NULL,
        movie_rating INT NOT NULL,
        movie_release DATE NOT NULL,
        movie_runtime INT NOT NULL,
        movie_collection INT NULL,
        movie_file_path TEXT NULL,
        movie_genres VARCHAR(255),
        movie_trailer VARCHAR(255),
        created TIMESTAMP,
        UNIQUE(movie_tmdbID),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
    
    //-- Genre Movie Table -- 
    $sql = 'CREATE TABLE IF NOT EXISTS movie_genre (
        id INT NOT NULL AUTO_INCREMENT,
        movie_id INT NOT NULL,
        genre_id INT NOT NULL,
        UNIQUE (movie_id, genre_id),
        FOREIGN KEY (movie_id) REFERENCES movies(movie_tmdbID),
        FOREIGN KEY (genre_id) REFERENCES genres(genre_id),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
    
    //-- Movie watch table -- 
    $sql = 'CREATE TABLE IF NOT EXISTS movie_watched (
        id INT NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        movie_id INT NOT NULL,
        watched_seconds DECIMAL(10,6),
        total_length DECIMAL(10,6),
        watched INT(1),
        last_watched TIMESTAMP,
        UNIQUE (user_id, movie_id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (movie_id) REFERENCES movies(movie_tmdbID),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    //-- TV Shows
    $sql = 'CREATE TABLE IF NOT EXISTS shows (
        id INT NOT NULL AUTO_INCREMENT,
        show_tmdbID INT,
        show_title TEXT,
        show_overview TEXT,
        show_poster TEXT,
        show_thumbnail TEXT,
        show_rating INT,
        show_release DATE,
        show_season_count INT,
        show_seasons TEXT,
        show_episodes_count INT,
        show_genres VARCHAR(255),
        show_trailer VARCHAR(255),
        created TIMESTAMP,
        UNIQUE(show_tmdbID),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    //-- Genre show Table -- 
    $sql = 'CREATE TABLE IF NOT EXISTS show_genre (
        id INT NOT NULL AUTO_INCREMENT,
        show_id INT NOT NULL,
        genre_id INT NOT NULL,
        UNIQUE (show_id, genre_id),
        FOREIGN KEY (show_id) REFERENCES shows(show_tmdbID),
        FOREIGN KEY (genre_id) REFERENCES genres(genre_id),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    //-- Show Seasons
    $sql = 'CREATE TABLE IF NOT EXISTS seasons (
        id INT NOT NULL AUTO_INCREMENT,
        season_tmdbID INT,
        season_title TEXT,
        season_overview TEXT,
        season_poster TEXT,
        season_number INT,
        season_rating INT,
        season_release DATE,
        season_episodes_count INT,
        season_show_tmdbID INT,
        created TIMESTAMP,
        UNIQUE(season_tmdbID),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    //-- Show Seasons
    $sql = 'CREATE TABLE IF NOT EXISTS seasons (
        id INT NOT NULL AUTO_INCREMENT,
        season_tmdbID INT,
        season_title TEXT,
        season_overview TEXT,
        season_poster TEXT,
        season_number INT,
        season_rating INT,
        season_release DATE,
        season_episodes_count INT,
        season_show_id INT,
        created TIMESTAMP,
        UNIQUE(season_tmdbID),
        FOREIGN KEY (season_show_id) REFERENCES shows(show_tmdbID),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    //-- Show Episodes
    $sql = 'CREATE TABLE IF NOT EXISTS episodes (
        id INT NOT NULL AUTO_INCREMENT,
        episode_tmdbID INT,
        episode_title TEXT,
        episode_overview TEXT,
        episode_thumbnail TEXT,
        episode_file_path TEXT,
        episode_number INT,
        episode_rating INT,
        episode_release DATE,
        episode_show_id INT,
        episode_season_id INT,
        created TIMESTAMP,
        UNIQUE(episode_tmdbID),
        FOREIGN KEY (episode_show_id) REFERENCES shows(show_tmdbID),
        FOREIGN KEY (episode_season_id) REFERENCES seasons(season_tmdbID),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
    
    //-- Movie watch table -- 
    $sql = 'CREATE TABLE IF NOT EXISTS watchlist (
        id INT NOT NULL AUTO_INCREMENT,
        user_id INT NOT NULL,
        movie_id INT NOT NULL,
        UNIQUE (user_id, movie_id),
        FOREIGN KEY (user_id) REFERENCES users(id),
        FOREIGN KEY (movie_id) REFERENCES movies(movie_tmdbID),
        PRIMARY KEY (id)
    )';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    //-- To fetch shows and movies at once
    $sql = "CREATE TABLE IF NOT EXISTS media (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        tmdbID INT,
        UNIQUE(tmdbID),
        type varchar(10)
    )";
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    $sql = "CREATE TABLE IF NOT EXISTS highlights (
        id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
        highlight_id INT,
        highlight_status BOOLEAN,
        FOREIGN KEY (highlight_id) REFERENCES media(tmdbID)
    )";
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    //-- One Time Setup Done --
    $sql = 'UPDATE settings SET setting_option="1" WHERE setting_name="one_time_setup"';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
};