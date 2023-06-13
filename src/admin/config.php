<?php 
$conn = dbConnect();
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    $result = $conn->query('Show DATABASES LIKE "'.DB_NAME.'"');
    $exists = $result->num_rows > 0;

    if (!$exists) {
        require_once ROOT_PATH.'/src/admin/db-setup.php';
    } else {
        $result = $conn->query('Show TABLES LIKE "settings"');
        $table_exists = $result->num_rows > 0;

        if (!$table_exists) {
            require_once ROOT_PATH.'/src/admin/table-setup.php';
        }
    }
}




