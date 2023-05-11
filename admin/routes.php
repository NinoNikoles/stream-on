<?php

require_once ROOT_PATH.'/admin/router.php';

// ##################################################
// ##################################################
// ##################################################


// Überprüfen, ob der Benutzer eingeloggt ist
if(!isset($_COOKIE['session_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    get('/', 'views/login.php');
    post('/', 'views/login.php');

    header('Location: /');
    exit();

} else {
    // Static GET
    // In the URL -> http://localhost
    // The output -> Index
    get('/', 'views/index.php');

    get('/register', 'views/register.php');
    post('/register', 'views/register.php');

    post('/login', 'login.php');

    get('/logout', 'views/logout.php');
    post('/logout', 'logout.php');

    // Admin Check
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    $sql = "SELECT role FROM users WHERE session='".$_COOKIE['session_id']."'";
    $result = $conn->query($sql);
    $role = $result->fetch_assoc();
    if ($role['role'] > 0) {
        get('/settings', 'views/backend/settings.php');
        post('/settings', 'views/backend/settings.php');

        get('/users', 'views/backend/users.php');
        post('/users', 'views/backend/users.php');

        get('/movies', 'views/backend/movies.php');
        post('/movies', 'views/backend/movies.php');

        get('/movies/add-movie', 'views/backend/add-movie.php');
        post('/movies/add-movie', 'views/backend/add-movie.php');

        get('/movies/movie-search', 'views/actions/movie-livesearch.php');
        post('/movies/movie-search', 'views/actions/movie-livesearch.php');

        get('/movies/edit-movie/$id', 'views/backend/single-movie.php');
        post('/movies/edit-movie/$id', 'views/backend/single-movie.php');

        get('/genres', 'views/backend/genres.php');
        post('/genres', 'views/backend/genres.php');
    }

    // Dynamic GET. Example with 1 variable
    // The $id will be available in user.php
    get('/user/$id', 'views/templates/user');
    post('/user/$id', 'views/templates/user');

    get('/user-img-upload', 'views/actions/user-image-upload.php');
    post('/user-img-upload', 'views/actions/user-image-upload.php');

    // Dynamic GET. Example with 2 variables
    // The $name will be available in full_name.php
    // The $last_name will be available in full_name.php
    // In the browser point to: localhost/user/X/Y
    get('/user/$name/$last_name', 'views/full_name.php');

    // Dynamic GET. Example with 2 variables with static
    // In the URL -> http://localhost/product/shoes/color/blue
    // The $type will be available in product.php
    // The $color will be available in product.php
    get('/product/$type/color/$color', 'product.php');

    // A route with a callback
    get('/callback', function(){
        echo 'Callback executed';
    });

    // A route with a callback passing a variable
    // To run this route, in the browser type:
    // http://localhost/user/A
    get('/callback/$name', function($name){
        echo "Callback executed. The name is $name";
    });

    // A route with a callback passing 2 variables
    // To run this route, in the browser type:
    // http://localhost/callback/A/B
    get('/callback/$name/$last_name', function($name, $last_name){
        echo "Callback executed. The full name is $name $last_name";
    });
}

// ##################################################
// ##################################################
// ##################################################
// any can be used for GETs or POSTs

// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404','views/404.php');
