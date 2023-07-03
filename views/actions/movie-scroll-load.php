<?php
$conn = dbConnect();
$tmdb = setupTMDB();

if ( $_POST['count'] === '' ) {   
    $loadCount = 0;
} else {
    $loadCount = $_POST['count'];
}

$movies = scrollLoader('movies', $loadCount);

$movieList = '';

if ( $movies > 0 ) {    
    foreach ( $movies as $movie ) {
        $movieList = $movieList . movie_card($movie, 'col-6 col-3-medium grid-padding');
    }

    $loadCount = count($movies);

    $response = array(
        'movieList' => $movieList,
        'loadCount' => $loadCount,
    );

    echo json_encode($response);
} else {
    $response = array(
        'movieList' => false,
        'loadCount' => false,
    );

    echo json_encode($response);
}