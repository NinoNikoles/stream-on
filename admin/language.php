<?php

class langSnippets {
    public $langs = [
        'en-US' => [
            //Menü
            'Settings' => 'Settings',
            'Movies' => 'Movies',
            'Users' => 'Users',
            'Shows' => 'TV Shows',
            'Genres' => 'Genres',
            'Logout' => 'Logout',

            // Universal
            'Cancel' => 'Cancel',
            'Save' => 'Save',
            'Add' => 'Add',
            'Edit' => 'Edit',
            'Remove' => 'Remove',
            'Home' => 'Home',
            'Delete' => 'Delete',
            'Add_user' => 'Add User',
            'Username' => 'Username',
            'Role' => 'Role',
            'ID' => 'ID',
            'TMDB_ID' => 'TMDB ID',
            'Name' => 'Name',
            'Hour' => 'Hour',
            'Hours' => 'Hours',
            'Minute' => 'Minute',
            'Minutes' => 'Minutes',

            // -- Messages --
            // Success
            'edit_user_success' => 'User edited successfully!',
            'add_user_success' => 'User added successfully!',
            'delete_user_success' => 'User deleted successfully!',

            'edit_movie_success' => 'Movie edited successfully!',
            'add_movie_success' => 'Movie added successfully!',
            'delete_movie_success' => 'Movie deleted successfully!',

            'genres_created_success' => 'Genres created successfully!',            

            'logout_message' => 'Successfully logged out!',
            'user_img_upload_success' => 'Image successfully uploaded!',

            // Alert
            'edit_user_alert' => 'User could not be edited!',
            'add_user_alert' => 'User could not be added!',
            'delete_user_alert' => 'User could not be deleted!',

            'edit_movie_alert' => 'Movie could not be edited!',
            'add_movie_alert' => 'Movie could not be added!',
            'delete_movie_alert' => 'Movie could not be deleted!',

            'genres_created_alert' => 'Genres could not be created!',            

            'user_img_upload_alert' => 'Img could not be uploaded!',

            // Warnings
            'no_movies_found' => 'No movies found!',
            'no_movies_available' => 'No movies available!',

            'user_img_upload_wrong_file' => 'The uploaded file must be an image (JPG, JPEG, PNG, GIF, SVG)!',
            'user_img_upload_no_file' => 'No image selected!',
        ],
        'de-DE' => [
            //Menü
            'Settings' => 'Einstellungen',
            'Movies' => 'Filme',
            'Users' => 'Benutzer',
            'Shows' => 'Serien',
            'Genres' => 'Genres',
            'Logout' => 'Ausloggen',

            // Universell
            'Cancel' => 'Abbrechen',
            'Save' => 'Speichern',
            'Add' => 'Hinzufügen',
            'Edit' => 'Bearbeiten',
            'Home' => 'Home',
            'Remove' => 'Entfernen',
            'Delete' => 'Löschen',
            'Add_user' => 'Benutzer hinzufügen',
            'Username' => 'Benutzername',
            'Role' => 'Rolle',
            'ID' => 'ID',
            'TMDB_ID' => 'TMDB ID',
            'Name' => 'Name',
            'Hour' => 'Stunde',
            'Hours' => 'Stunden',
            'Minute' => 'Minute',
            'Minutes' => 'Minuten',

            // -- Messages --
            // Success
            'edit_user_success' => 'Der Benutzer wurde erfolgreich bearbeitet!',
            'add_user_success' => 'Der Benutzer wurde erfolgreich hinzugefügt!',
            'delete_user_success' => 'Der Benutzer wurde erfolgreich gelöscht!',

            'edit_movie_success' => 'Der Film wurde erfolgreich bearbeitet!',
            'add_movie_success' => 'Der Film wurde erfolgreich hinzugefügt!',
            'delete_movie_success' => 'Der Film wurde erfolgreich gelöscht!',

            'genres_created_success' => 'Genre wurden erfolgreich geladen!',            

            'logout_message' => 'Du hast dich erfolgreich abgemeldet!',
            'user_img_upload_success' => 'Bild erfolgreich hochgeladen',

            // Alert
            'edit_user_alert' => 'Der Benutzer konnte nicht bearbeitet werden!',
            'adduseralert' => 'Der Benutzer konnte nicht hinzugefügt werden!',
            'deleteuseralert' => 'Der Benutzer konnte nicht gelöscht werden!',

            'edit_movie_alert' => 'Der Film konnte nicht bearbeitet werden!',
            'add_movie_alert' => 'Der Film konnte nicht hinzugefügt werden!',
            'delete_movie_alert' => 'Der Film konnte nicht gelöscht werden!',

            'genres_created_alert' => 'Genre konntent nicht geladen werden!',            

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

        if (array_key_exists($langSnippet, $this->langs[$lang])) {
            return $this->langs[$lang][$langSnippet];
        } else {
            return $this->langs['en-US'][$langSnippet];
        }        
    }
}

function lang_snippet($langSnippet) {
    $languages = new langSnippets();

    return $languages->get_lang($langSnippet);  
}
    
    