<?php

$conn = dbConnect();

//-- Settings Table --
$sql = 'CREATE TABLE IF NOT EXISTS settings (
    id INT NOT NULL AUTO_INCREMENT,
    setting_name varchar(255),
    setting_option varchar(255),
    UNIQUE (setting_name),
    PRIMARY KEY (id)
)';
if (!($conn->query($sql) === TRUE)) {
    die('Error creating table: ' . $conn->error);
} else {
    $sql = 'INSERT INTO settings(setting_name, setting_option) VALUES 
    ("one_time_setup", "0"),
    ("apikey", NULL),
    ("apilang", NULL),
    ("site_title", "Stream On")';
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
    $username = 'admin';
    $password = 'admin';
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
    movie_title VARCHAR(255) NOT NULL,
    movie_tagline VARCHAR(255) NOT NULL,
    movie_overview TEXT NOT NULL,
    movie_poster TEXT NOT NULL,
    movie_thumbnail TEXT NOT NULL,
    movie_rating INT NOT NULL,
    movie_release DATE NOT NULL,
    movie_runtime INT NOT NULL,
    movie_collection INT NULL,
    movie_file_path TEXT NULL,
    movie_genres VARCHAR(255),
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
    watched INT(1) NOT NULL,
    last_watched TIMESTAMP,
    UNIQUE (user_id, movie_id),
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (movie_id) REFERENCES movies(movie_tmdbID),
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

//-- One Time Setup Done --
$sql = 'UPDATE settings SET setting_option="1" WHERE setting_name="one_time_setup"';
if (!($conn->query($sql) === TRUE)) {
    die('Error creating table: ' . $conn->error);
}