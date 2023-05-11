<?php

$creat_db = 'CREATE DATABASE ' . DB_NAME;
if ($conn->query($creat_db) === TRUE) {
    echo 'Database created successfully';
    header('Location: /login');
    exit();
} else {
    die('Error creating database: ' . $conn->error);
}