<?php

$creat_db = 'CREATE DATABASE ' . DB_NAME;
if ($conn->query($creat_db) === TRUE) {
    echo 'Database created successfully';
    page_redirect("/");
} else {
    die('Error creating database: ' . $conn->error);
}