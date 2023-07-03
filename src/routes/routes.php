<?php

get('/', 'views/index.php');

get('/login', 'views/login.php');
post('/login', 'views/login.php');

get('/register', 'views/register.php');
post('/register', 'views/register.php');

get('/logout', 'views/logout.php');
post('/logout', 'views/logout.php');    

// Dynamic GET. Example with 1 variable
// The $id will be available in user.php
get('/user/$id', 'views/templates/user');
post('/user/$id', 'views/templates/user');

get('/user-img-upload', 'views/actions/user-image-upload.php');
post('/user-img-upload', 'views/actions/user-image-upload.php');

// Movie Page
get('/movies', 'views/movies');
post('/movies', 'views/movies');

// Mediaplayer page
get('/watch/$id', 'views/templates/watch');
post('/watch/$id', 'views/templates/watch');

// Search page
get('/search', 'views/search.php');
post('/search', 'views/search.php');

// Search page
get('/my-list', 'views/my-list.php');
post('/my-list', 'views/my-list.php');


/////////////////////////////////////////////////////////////////////////////////////////////////
//-- AJAX STUFF --

// Ajax add to my list
get('/add-to-list', 'views/actions/add-to-list.php');
post('/add-to-list', 'views/actions/add-to-list.php');

// Ajax searchbar
get('/searchbar', 'views/actions/searchbar.php');
post('/searchbar', 'views/actions/searchbar.php');

post('/movie-watch-time', 'views/actions/movie-watched-time.php');

// Ajax DB media search
get('/live-search', 'views/actions/livesearch.php');
post('/live-search', 'views/actions/livesearch.php');

// Ajax movie scroll load
get('/movie-scroll-load', 'views/actions/movie-scroll-load.php');
post('/movie-scroll-load', 'views/actions/movie-scroll-load.php');


/////////////////////////////////////////////////////////////////////////////////////////////////
// Admin Check
if ($_SESSION['role'] == '1') {
    get('/admin/settings', 'views/backend/settings.php');
    post('/admin/settings', 'views/backend/settings.php');

    get('/admin/users', 'views/backend/users.php');
    post('/admin/users', 'views/backend/users.php');

    get('/admin/movies', 'views/backend/movies.php');
    post('/admin/movies', 'views/backend/movies.php');

    get('/admin/movies/add-movie', 'views/backend/add-movie.php');
    post('/admin/movies/add-movie', 'views/backend/add-movie.php');

    get('/admin/movies/movie-api-search', 'views/actions/movie-api-search.php');
    post('/admin/movies/movie-api-search', 'views/actions/movie-api-search.php');

    get('/admin/file-api', 'views/actions/file-api.php');
    post('/admin/file-api', 'views/actions/file-api.php');

    get('/admin/movie/$id', 'views/backend/single-movie.php');
    post('/admin/movie/$id', 'views/backend/single-movie.php');

    get('/admin/genres', 'views/backend/genres.php');
    post('/admin/genres', 'views/backend/genres.php');
}


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

// For GET or POST
// The 404.php which is inside the views folder will be called
// The 404.php has access to $_GET and $_POST
any('/404','views/404.php');