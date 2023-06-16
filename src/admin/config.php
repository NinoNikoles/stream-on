<?php 
$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);


if ($conn->connect_error) {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
    $result = $conn->query('Show DATABASES LIKE "'.DB_NAME.'"');
    $exists = $result->num_rows > 0;

    if (!$exists) {
        require_once ROOT_PATH.'/src/admin/db-setup.php';
    } else {
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
        $result = $conn->query('Show TABLES LIKE "settings"');
        $table_exists = $result->num_rows > 0;

        if (!$table_exists) {
            require_once ROOT_PATH.'/src/admin/table-setup.php';
        }
    }
} else {
    $result = $conn->query('Show TABLES LIKE "settings"');
    $table_exists = $result->num_rows > 0;

    if (!$table_exists) {
        require_once ROOT_PATH.'/src/admin/table-setup.php';
    }
}




