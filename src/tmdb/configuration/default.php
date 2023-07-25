<?php

$conn = dbConnect();

$sql = "SELECT setting_name, setting_option FROM settings";
$result = $conn->query($sql);

//------------------------------------------------------------------------------
// Configuration to get all data
//------------------------------------------------------------------------------

$configData = [];
while ( $row = $result->fetch_assoc() ) {
    $configData[$row['setting_name']] = $row['setting_option'];
}

// Global Configuration
$cnf['apikey'] = $configData['apikey'];
$cnf['site_title'] = $configData['site_title'];
$cnf['lang'] = $configData['apilang'];
$cnf['enable_edit_btn'] = $configData['enable_edit_btn'];
$cnf['timezone'] = 'Europe/Berlin';
$cnf['adult'] = false;
$cnf['debug'] = false;

// Data Return Configuration - Manipulate if you want to tune your results
$cnf['appender']['movie'] = array('account_states', 'alternative_titles', 'credits', 'images','keywords', 'release_dates', 'videos', 'translations', 'similar', 'reviews', 'lists', 'changes', 'rating');
$cnf['appender']['tvshow'] = array('account_states', 'alternative_titles', 'changes', 'content_rating', 'credits', 'external_ids', 'images', 'keywords', 'rating', 'similar', 'translations', 'videos');
$cnf['appender']['season'] = array('changes', 'account_states', 'credits', 'external_ids', 'images', 'videos');
$cnf['appender']['episode'] = array('changes', 'account_states', 'credits', 'external_ids', 'images', 'rating', 'videos');
$cnf['appender']['person'] = array('movie_credits', 'tv_credits', 'combined_credits', 'external_ids', 'images', 'tagged_images', 'changes');
$cnf['appender']['collection'] = array('images');
$cnf['appender']['company'] = array('movies');

?>