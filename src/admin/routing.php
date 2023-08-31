<?php

class routes {
    public $routes = [
        'public' => [
            '/' => 'views/index.php',
            '/login' => 'views/login.php',
            '/register' => 'views/register.php',
            '/logout' => 'views/logout.php',

            '/user/$id' => 'views/templates/user.php',
            '/user-img-upload' => 'views/actions/user-image-upload.php',

            '/movies' => 'views/movies.php',
            '/shows' => 'views/shows.php',
            '/watch/$id' => 'views/templates/watch.php',
            '/watchtogether/$id' => 'views/templates/watchtogether.php',
            '/search' => 'views/search.php',
            '/my-list' => 'views/my-list.php',
        ],
        'ajax' => [
            '/add-to-list' => 'views/actions/add-to-list.php',
            '/searchbar' => 'views/actions/searchbar.php',
            '/movie-watch-time' => 'views/actions/movie-watched-time.php',
            '/live-search' => 'views/actions/livesearch.php',
            '/highlight-status' => 'views/actions/highlight-status.php',
            '/media-popup' => 'views/actions/media-popup.php',
            '/filter' => 'views/actions/filter.php',
            '/save-volume' => 'views/actions/save-volume.php',
            '/msg' => 'views/actions/send-message.php',
        ],
        'admin' => [
            '/admin/settings' => 'views/backend/settings.php',
            '/admin/users' => 'views/backend/users.php',
            '/admin/movies' => 'views/backend/movies.php',
            '/admin/movies/add-movie' => 'views/backend/add-movie.php',
            '/admin/movies/movie-api-search' => 'views/actions/movie-api-search.php',
            '/admin/shows/show-api-search' => 'views/actions/show-api-search.php',
            '/admin/file-api' => 'views/actions/file-api.php',
            '/admin/episode-file-path' => 'views/actions/episode-path.php',
            '/admin/movie/$id' => 'views/backend/single-movie.php',
            '/admin/show/$id' => 'views/backend/single-show.php',
            '/admin/genres' => 'views/backend/genres.php',
            '/admin/highlights' => 'views/backend/highlights.php',
            '/admin/shows' => 'views/backend/shows.php',
        ],
        'error' => [
            '/404' => 'views/404.php'
        ]
    ];

    
	public function get_routes($category) {
        if (isset($this->routes[$category])) {
            foreach ($this->routes[$category] as $route => $path) {
                any($route, $path);
            }
        } else {
            echo "Category '$category' not found.";
        }
    }
}

function routes($category) {
    $routesInstance = new routes();
    return $routesInstance->get_routes($category);
}
