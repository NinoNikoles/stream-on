<?php

$conn = dbConnect();
$values = array(
    'site_title' => mysqli_real_escape_string($conn, $_POST['site_title']),
    'apikey' => mysqli_real_escape_string($conn, $_POST['apikey']),
    'language' => mysqli_real_escape_string($conn, $_POST['language']),
    'enable_edit' => mysqli_real_escape_string($conn, $_POST['enable_edit']),
);

updateSettings($values);
?>