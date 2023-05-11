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
            'Add user' => 'Add User',
            'Username' => 'Username',
            'Role' => 'Role',
            'ID' => 'ID',
            'TMDB ID' => 'TMDB ID',
            'Name' => 'Name',
            'Hour' => 'Hour',
            'Hours' => 'Hours',
            'Minute' => 'Minute',
            'Minutes' => 'Minutes',

            // -- Messages --
            // Success
            'editusersuccess' => 'User edited successfully!',
            'addusersuccess' => 'User added successfully!',
            'deleteusersuccess' => 'User deleted successfully!',

            'editmoviesuccess' => 'Movie edited successfully!',
            'addmoviesuccess' => 'Movie added successfully!',
            'deletemoviesuccess' => 'Movie deleted successfully!',

            'genres created success' => 'Genres created successfully!',            

            'logout message' => 'Successfully logged out!',
            'user img upload success' => 'Img successfully uploaded!',

            // Alert
            'edituseralert' => 'User could not be edited!',
            'adduseralert' => 'User could not be added!',
            'deleteuseralert' => 'User could not be deleted!',

            'editmoviealert' => 'Movie could not be edited!',
            'addmoviealert' => 'Movie could not be added!',
            'deletemoviealert' => 'Movie could not be deleted!',

            'genres created alert' => 'Genres could not be created!',            

            'user img upload alert' => 'Img could not be uploaded!',

            // Warnings
            'No movies found' => 'Es konnten keine Filme gefunden werden!',
            'No movies available' => 'No movies available!',

            'user img upload wrong file' => 'Das hochgeladene File muss ein Bild sein (JPG, JPEG, PNG, GIF, SVG)!',
            'user img upload no file' => 'Es wurde kein Bild ausgewählt!',
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
            'Add user' => 'Benutzer hinzufügen',
            'Username' => 'Benutzername',
            'Role' => 'Rolle',
            'ID' => 'ID',
            'TMDB ID' => 'TMDB ID',
            'Name' => 'Name',
            'Hour' => 'Stunde',
            'Hours' => 'Stunden',
            'Minute' => 'Minute',
            'Minutes' => 'Minuten',

            // -- Messages --
            // Success
            'editusersuccess' => 'Der Benutzer wurde erfolgreich bearbeitet!',
            'addusersuccess' => 'Der Benutzer wurde erfolgreich hinzugefügt!',
            'deleteusersuccess' => 'Der Benutzer wurde erfolgreich gelöscht!',

            'editmoviesuccess' => 'Der Film wurde erfolgreich bearbeitet!',
            'addmoviesuccess' => 'Der Film wurde erfolgreich hinzugefügt!',
            'deletemoviesuccess' => 'Der Film wurde erfolgreich gelöscht!',

            'logout message' => 'Du hast dich erfolgreich abgemeldet!',

            // Alert
            'edituseralert' => 'Der Benutzer konnte nicht bearbeitet werden!',
            'adduseralert' => 'Der Benutzer konnte nicht hinzugefügt werden!',
            'deleteuseralert' => 'Der Benutzer konnte nicht gelöscht werden!',

            'editmoviealert' => 'Der Film konnte nicht bearbeitet werden!',
            'addmoviealert' => 'Der Film konnte nicht hinzugefügt werden!',
            'deletemoviealert' => 'Der Film konnte nicht gelöscht werden!',

            // Warnings
            'No movies found' => 'Es konnten eine Filme gefunden werden!',
            'No movies available' => 'Keine Filme verfügbar!',
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
    
    