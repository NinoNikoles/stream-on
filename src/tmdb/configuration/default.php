<?php

$conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

// Get API Key
$sql = "SELECT setting_option FROM settings WHERE setting_name='apikey'";
$result = $conn->query($sql);
$apikey = $result->fetch_assoc();
$apikey = $apikey['setting_option'];

// GET Site title
$sql = "SELECT setting_option FROM settings WHERE setting_name='site_title'";
$result = $conn->query($sql);
$site_title = $result->fetch_assoc();
$site_title = $site_title['setting_option'];

// Get API Language
$sql = "SELECT setting_option FROM settings WHERE setting_name='apilang'";
$result = $conn->query($sql);
$apiLang = $result->fetch_assoc();
$apiLang = $apiLang['setting_option'];
//------------------------------------------------------------------------------
// Configuration to get all data
//------------------------------------------------------------------------------

// Global Configuration
$cnf['apikey'] = $apikey;
$cnf['site_title'] = $site_title;
$cnf['lang'] = $apiLang;
$cnf['timezone'] = 'Europe/Berlin';
$cnf['adult'] = true;
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