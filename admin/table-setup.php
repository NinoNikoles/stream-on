<?php

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Settings Table
$create_table_settings = 'CREATE TABLE IF NOT EXISTS settings (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    setting_name varchar(255) UNIQUE,
    setting_option varchar(255)
)';

if (!($conn->query($create_table_settings) === TRUE)) {
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

// User Table
$create_table_user = 'CREATE TABLE IF NOT EXISTS users (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    username varchar(255) UNIQUE,
    firstname varchar(255),
    lastname varchar(255),
    password varchar(255),
    user_img varchar(255),
    role BOOLEAN,
    session varchar(255),
    created TIMESTAMP
)';

if (!($conn->query($create_table_user) === TRUE)) {
    die('Error creating table: ' . $conn->error);
} else {
    $username = 'admin';
    $password = 'admin';
    $role = 1;
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $createAdmin = "INSERT INTO users (username, password, role) VALUES ('$username', '$hashed_password', '$role')";
    if (!($conn->query($createAdmin) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }
}

// Shows Genre Table
$create_table_genres = 'CREATE TABLE IF NOT EXISTS genres (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    genre_id INT UNSIGNED UNIQUE,
    created TIMESTAMP
)';
if (!($conn->query($create_table_genres) === TRUE)) {
    die('Error creating showsGenre: ' . $conn->error);
}

// Movie Table
$create_table_movies = 'CREATE TABLE IF NOT EXISTS movies (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    movie_tmdbID INT NOT NULL,
    movie_title varchar(255),
    movie_poster varchar(255),
    movie_thumbnail varchar(255),
    created TIMESTAMP,
    UNIQUE (movie_tmdbID)
)';

if (!($conn->query($create_table_movies) === TRUE)) {
    die('Error creating table: ' . $conn->error);
}

$sql = 'UPDATE settings SET setting_option="1" WHERE setting_name="one_time_setup"';
if (!($conn->query($sql) === TRUE)) {
    die('Error creating table: ' . $conn->error);
}

header('Location: /login');
exit();