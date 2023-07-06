<?php

class langSnippets {
    public $langs = [
        'en-US' => [
            // Menü
            'settings' => 'Settings',
            'movies' => 'Movies',
            'users' => 'Users',
            'shows' => 'TV Shows',
            'genres' => 'Genres',
            'logout' => 'Logout',

            // Universal
            'cancel' => 'Cancel',
            'save' => 'Save',
            'add' => 'Add',
            'edit' => 'Edit',
            'remove' => 'Remove',
            'home' => 'Home',
            'delete' => 'Delete',
            'add_user' => 'Add User',
            'username' => 'Username',
            'role' => 'Role',
            'id' => 'ID',
            'tmdb_id' => 'TMDB ID',
            'name' => 'Name',
            'hour' => 'Hour',
            'hours' => 'Hours',
            'minute' => 'Minute',
            'minutes' => 'Minutes',
			'api_key' => 'API Key',
			'language' => 'Language',
            'page_title' => 'Page title',
            'profile' => 'Profile',
            'more_informations' => 'More infromations',
            'list' => 'My List',
			
			'load_genres' => 'Load genres',
			'movie_title' => 'Movie title',
            'continue' => 'Continue',
            'my_list' => 'My list',
            'apikey_info'=> 'You need an apikey to fetch fetch infos from the movie database. Klick <a href="https://www.themoviedb.org/settings/api" target="_blank" title="TMDB">here</a> to get your own key!',
            'lang_info' => 'The language is need to fetch all infos in your desired language. Format: language-COUNTRY',
			
			// Movie
			'rating' => 'Rating',
			'runtime' => 'Runtime',
			'release_date' => 'Release date',
			
			'select_new_poster' => 'Select a new poster',
			'select_new_thumbnail' => 'Select a new thumbnail',
            'select_movie_file' => 'Select a movie file',
			'add_movie' => 'Would you like to add the movie?',

            // -- Messages --
            // Success
            'settings_update_success' => 'Settings updated successfully!',
            'edit_user_success' => 'User edited successfully!',
            'add_user_success' => 'User added successfully!',
            'delete_user_success' => 'User deleted successfully!',

            'edit_movie_success' => 'Movie edited successfully!',
            'add_movie_success' => 'Movie added successfully!',
            'delete_movie_success' => 'Movie deleted successfully!',

            'genres_created_success' => 'Genres created successfully!',            
            'update_poster_success' => 'Poster updated successfully!',            
            'update_backdrop_success' => 'Thumbnail updated successfully!',         
            'update_file_path_success' => 'File path updated successfully!',         

            'logout_message' => 'Successfully logged out!',
            'user_img_upload_success' => 'Image successfully uploaded!',

            // Alert
			'failed_to_save' => 'Save failed!',
            'edit_user_alert' => 'User could not be edited!',
            'add_user_alert' => 'User could not be added!',
            'delete_user_alert' => 'User could not be deleted!',

            'edit_movie_alert' => 'Movie could not be edited!',
            'add_movie_alert' => 'Movie could not be added!',
            'delete_movie_alert' => 'Movie could not be deleted!',

            'genres_created_alert' => 'Genres could not be created!', 
            'update_poster_alert' => 'Poster could not be updated!',            
            'update_backdrop_alert' => 'Thumbnail could not be updated!',         
            'update_file_path_alert' => 'File path could not be updated!',  			

            'user_img_upload_alert' => 'Img could not be uploaded!',

            // Warnings
            'no_movies_found' => 'No movies found!',
            'no_movies_available' => 'No movies available!',

            'user_img_upload_wrong_file' => 'The uploaded file must be an image (JPG, JPEG, PNG, GIF, SVG)!',
            'user_img_upload_no_file' => 'No image selected!',
        ],
        'de-DE' => [
            // Menü
            'settings' => 'Einstellungen',
            'movies' => 'Filme',
            'users' => 'Benutzer',
            'shows' => 'Serien',
            'genres' => 'Genre',
            'logout' => 'Ausloggen',

            // Universell
            'cancel' => 'Abbrechen',
            'save' => 'Speichern',
            'add' => 'Hinzufügen',
            'edit' => 'Bearbeiten',
            'home' => 'Home',
            'remove' => 'Entfernen',
            'delete' => 'Löschen',
            'add_user' => 'Benutzer hinzufügen',
            'username' => 'Benutzername',
            'role' => 'Rolle',
            'id' => 'ID',
            'tmdb_id' => 'TMDB ID',
            'name' => 'Name',
            'hour' => 'Stunde',
            'hours' => 'Stunden',
            'minute' => 'Minute',
            'minutes' => 'Minuten',
			'api_key' => 'API Key',
			'language' => 'Sprache',
            'page_title' => 'Seitenname',
            'profile' => 'Profil',
            'more_informations' => 'Mehr erfahren',
            'list' => 'Meine Liste',
			
			'load_genres' => 'Genres laden',
			'movie_title' => 'Movie Title',
            'continue' => 'Fortsetzen',
            'my_list' => 'Meine Liste',
			
			// Movie
			'rating' => 'Bewertung',
			'runtime' => 'Dauer',
			'release_date' => 'Erscheinungsdatum',
			
			'select_new_poster' => 'Wähle ein neues Poster',
			'select_new_thumbnail' => 'Wähle ein neues Thumbnail',
            'select_movie_file' => 'Wähle eine Video Datei aus',
			'add_movie' => 'Möchtest du den Film hinzufügen?',

            'apikey_info'=> 'Du brauchst einen eigenen API Key von TMDB um Inhalte laden zu können. Klicke <a href="https://www.themoviedb.org/settings/api" target="_blank" title="TMDB">hier</a> um einen Schlüssel zu bekommen!',
            'lang_info' => 'Dies wird benötigt um alle Inhalte in der gewünschten Sprache zu laden. Format: language-COUNTRY',

            // -- Messages --
            // Success
            'settings_update_success' => 'Einstellungen wurden erfolgreich aktualisiert!',
            'edit_user_success' => 'Der Benutzer wurde erfolgreich bearbeitet!',
            'add_user_success' => 'Der Benutzer wurde erfolgreich hinzugefügt!',
            'delete_user_success' => 'Der Benutzer wurde erfolgreich gelöscht!',

            'edit_movie_success' => 'Der Film wurde erfolgreich bearbeitet!',
            'add_movie_success' => 'Der Film wurde erfolgreich hinzugefügt!',
            'delete_movie_success' => 'Der Film wurde erfolgreich gelöscht!',

            'genres_created_success' => 'Genre wurden erfolgreich geladen!',
            'update_poster_success' => 'Poster wurde erfolgreich aktualisiert!',            
            'update_backdrop_success' => 'Thumbnail wurde erfolgreich aktualisiert!',         
            'update_file_path_success' => 'Dateipfad wurde erfolgreich aktualisiert!',    			

            'logout_message' => 'Du hast dich erfolgreich abgemeldet!',
            'user_img_upload_success' => 'Bild erfolgreich hochgeladen',

            // Alert
			'failed_to_save' => 'Speichern fehlgeschlagen!',
            'edit_user_alert' => 'Der Benutzer konnte nicht bearbeitet werden!',
            'add_user_alert' => 'Der Benutzer konnte nicht hinzugefügt werden!',
            'delete_user_alert' => 'Der Benutzer konnte nicht gelöscht werden!',

            'edit_movie_alert' => 'Der Film konnte nicht bearbeitet werden!',
            'add_movie_alert' => 'Der Film konnte nicht hinzugefügt werden!',
            'delete_movie_alert' => 'Der Film konnte nicht gelöscht werden!',

            'genres_created_alert' => 'Genre konntent nicht geladen werden!',
            'update_poster_alert' => 'Poster konntent nicht aktualisiert werden!',            
            'update_backdrop_alert' => 'Thumbnail konntent nicht aktualisiert werden!',         
            'update_file_path_alert' => 'Dateipfad konntent nicht aktualisiert werden!',  			

            'user_img_upload_alert' => 'Bild konnte hochgeladen werden!',

            // Warnings
            'no_movies_found' => 'Es konnten eine Filme gefunden werden!',
            'no_movies_available' => 'Keine Filme verfügbar!',

            'user_img_upload_wrong_file' => 'Das hochgeladene File muss ein Bild sein (JPG, JPEG, PNG, GIF, SVG)!',
            'user_img_upload_no_file' => 'Es wurde kein Bild ausgewählt!',
        ]
    ];

    public function get_lang($langSnippet) {
        $lang = get_browser_language();

        if (isset($this->langs[$lang]) && array_key_exists($langSnippet, $this->langs[$lang])) {
            return $this->langs[$lang][$langSnippet];
        } else {
            if (isset($this->langs['en-US'][$langSnippet]) && array_key_exists($langSnippet, $this->langs['en-US'])) {
                return $this->langs['en-US'][$langSnippet];
            } else {
                return '';
            }
        }        
    }
}

function lang_snippet($langSnippet) {
    $languages = new langSnippets();

    return $languages->get_lang($langSnippet);  
}
    
    