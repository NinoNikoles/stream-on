<?php

$conn = dbConnect();

//-- Settings Table --
$sql = 'CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    setting_name varchar(255) UNIQUE,
    setting_option varchar(255)
)';
if (!($conn->query($sql) === TRUE)) {
    die('Error creating table: ' . $conn->error);
} else {
    $sql = 'INSERT INTO settings(setting_name, setting_option) VALUES ("one_time_setup", "0")';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
    $sql = 'INSERT INTO settings(setting_name) VALUES ("apikey")';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
    $sql = 'INSERT INTO settings(setting_name) VALUES ("apilang")';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
}

//-- User Table --
$sql = 'CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username varchar(255) UNIQUE,
    firstname varchar(255),
    lastname varchar(255),
    password varchar(255),
    user_img varchar(255),
    role BOOLEAN,
    created TIMESTAMP
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
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    genre_id INT UNSIGNED UNIQUE,
    genre_name VARCHAR(255) NOT NULL,
    created TIMESTAMP
)';
if (!($conn->query($sql) === TRUE)) {
    die('Error creating showsGenre: ' . $conn->error);
}

//-- Movie Table --
$sql = 'CREATE TABLE IF NOT EXISTS movies (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    movie_tmdbID INT NOT NULL,
    movie_title VARCHAR(255) NOT NULL,
    movie_tagline VARCHAR(255) NOT NULL,
    movie_overview TEXT NOT NULL,
    movie_poster VARCHAR(255) NOT NULL,
    movie_thumbnail VARCHAR(255) NOT NULL,
    movie_rating INT NOT NULL,
    movie_release DATE NOT NULL,
    movie_runtime INT NOT NULL,
    movie_collection INT NOT NULL,
    movie_genres VARCHAR(255) NOT NULL,
    movie_file_path VARCHAR(255) NOT NULL,
    created TIMESTAMP,
    UNIQUE (movie_tmdbID)
)';
if (!($conn->query($sql) === TRUE)) {
    die('Error creating table: ' . $conn->error);
}

//-- One Time Setup Done --
$sql = 'UPDATE settings SET setting_option="1" WHERE setting_name="one_time_setup"';
if (!($conn->query($sql) === TRUE)) {
    die('Error creating table: ' . $conn->error);
}

page_redirect("/login");