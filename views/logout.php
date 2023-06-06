<?php
    include(ROOT_PATH.'/views/head.php');

    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

    $sql = 'UPDATE users SET session="" WHERE session="'.$_COOKIE['session_id'].'"';
    if (!($conn->query($sql) === TRUE)) {
        die('Error creating table: ' . $conn->error);
    }

    set_callout('success','logout_message');

    setcookie('PHPSESSID', '', time() - 3600, "/");
    setcookie('session_id', '', time() - 3600, "/");
    session_unset();
    session_destroy();
    session_write_close();
    header('Location: /');
    exit();
?>