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
            'login' => 'Login',
            'logout' => 'Logout',
            'highlights' => 'Highlights',

            // Universal
            'cancel' => 'Cancel',
            'save' => 'Save',
            'add' => 'Add',
            'edit' => 'Edit',
            'remove' => 'Remove',
            'home' => 'Home',
            'delete' => 'Delete',
            'update' => 'Update',
            'add_user' => 'Add User',
            'username' => 'Username',
            'user_img' => 'User image',
            'password' => 'Password',
            'role' => 'Role',
            'admin' => 'Admin',
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
            'trailer' => 'Trailer',
            'highlight' => 'Highlight',
            'status' => 'Status',
            'episode' => 'Episode',
            'episodes' => 'Episodes',
            'season' => 'Season',
            'seasons' => 'Seasons',
            'watch_now' => 'Watch now',
            'continue_watching' => 'Continue',
            'all' => 'All',
            'menu' => 'Menu',
            'search' => 'Search',
            'similar' => 'Similar',

			'load_genres' => 'Load genres',
			'movie_title' => 'Movie title',
            'continue' => 'Continue',
            'my_list' => 'My list',
            'add_highlight' => 'Add to highlights',
            'apikey_info'=> 'You need an apikey to fetch fetch infos from the movie database. Klick <a href="https://www.themoviedb.org/settings/api" class="smaller" target="_blank" title="TMDB">here</a> to get your own key!',
            'lang_info' => 'The language is need to fetch all infos in your desired language. Format: language-COUNTRY (example: en-US)',
			'trailer_info' => 'ID of the YouTube video URL. Example: https://www.youtube.com/watch?v=<strong>iUISLveqaII</strong>',
            'back_to_homepage' => 'Back to homepage',
            'admin_setup_here' => 'Please update the page settings <a href="/admin/settings"><strong>here</strong></a> to be able to add your content.',
            'pls_wait_for_admin_setup' => 'No content has been added yet. Please be patient a little longer.',
            'loading_content' => 'Loading content, please wait.',
            'edit_user' => 'Edit user',
            'enable_edit_btn' => 'Do you want to allow media editing from the main pages? (concerns admins only)',
            'next_episode' => 'Next episode',
            'sorting' => 'Order',
            'no_content_found' => 'No content was found.',
            'has_joined_the_chat' => 'has joined the chat.',
            'message' => 'Message',
            'send' => 'Send',
            'upload_new_img' => 'Upload new profile image',
            'all_uploads' => 'All uploads',
            'search_result' => 'Search results for:',

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

            'add_show_success' => 'Show added successfully!',
            'edit_show_success' => 'Show edited successfully!',
            'delete_show_success' => 'Show deleted successfully!',
            'update_show_success' => 'Show updated successfully!',

            'genres_created_success' => 'Genres created successfully!',
            'update_poster_success' => 'Poster updated successfully!',
            'update_backdrop_success' => 'Thumbnail updated successfully!',
            'update_file_path_success' => 'File path updated successfully!',
            'update_trailer_success' => 'Trailer updated successfully!',
            
            'add_highlight_success' => 'Highlight added successfully!', 
            'delete_highlight_success' => 'Highlight deleted successfully!', 
            'delete_season_success' => 'Season deleted successfully!',

            'logout_message' => 'Successfully logged out!',
            'user_img_upload_success' => 'Image successfully uploaded!',
            'user_img_update_success' => 'Image successfully updated!',

            // Alert
			'failed_to_save' => 'Save failed!',
            'edit_user_alert' => 'User could not be edited!',
            'add_user_alert' => 'User could not be added!',
            'delete_user_alert' => 'User could not be deleted!',

            'edit_movie_alert' => 'Movie could not be edited!',
            'add_movie_alert' => 'Movie could not be added!',
            'delete_movie_alert' => 'Movie could not be deleted!',

            'add_show_alert' => 'Show could not be added!',
            'edit_show_alert' => 'Show could not be edited!',
            'delete_show_alert' => 'Show could not be deleted!',
            'update_show_alert' => 'Show could not be updated!',

            'genres_created_alert' => 'Genres could not be created!',
            'update_poster_alert' => 'Poster could not be updated!',
            'update_backdrop_alert' => 'Thumbnail could not be updated!',
            'update_file_path_alert' => 'File path could not be updated!',
            'update_trailer_alert' => 'Trailer could not be updated!',

            'add_highlight_alert' => 'Highlight could not be added!', 
            'delete_highlight_alert' => 'Highlight could not be deleted!',
            'delete_season_alert' => 'Season could not be deleted!',

            'user_img_upload_alert' => 'Img could not be uploaded!',

            'username_pw_wrong' => 'Username or password not correct!',

            // Warnings
            'username_not_found' => 'Username not found!',
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
            'login' => 'Anmelden',
            'logout' => 'Ausloggen',
            'highlights' => 'Highlights',

            // Universell
            'cancel' => 'Abbrechen',
            'save' => 'Speichern',
            'add' => 'Hinzufügen',
            'edit' => 'Bearbeiten',
            'home' => 'Home',
            'remove' => 'Entfernen',
            'delete' => 'Löschen',
            'update' => 'Aktualisieren',
            'add_user' => 'Benutzer hinzufügen',
            'username' => 'Benutzername',
            'user_img' => 'Benutzerbild',
            'password' => 'Passwort',
            'role' => 'Rolle',
            'admin' => 'Admin',
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
            'trailer' => 'Trailer',
            'highlight' => 'Highlight',
            'status' => 'Status',
            'episode' => 'Folge',
            'episodes' => 'Folgen',
            'season' => 'Staffel',
            'seasons' => 'Staffeln',
            'all' => 'Alle',
            'menu' => 'Menü',
            'upload_new_img' => 'Lade ein neues Benutzerbild hoch',
            'all_uploads' => 'Alle Uploads',
            'similar' => 'Ähnliche',

			'load_genres' => 'Genres laden',
			'movie_title' => 'Movie Title',
            'continue' => 'Fortsetzen',
            'my_list' => 'Meine Liste',
			'add_highlight' => 'Zu Highlights hinzufügen',
            'watch_now' => 'Jetzt schauen',
            'continue_watching' => 'Fortsetzen',
            'page_not_found' => 'Seite nicht gefunden',
            'back_to_homepage' => 'Zurück zur Startseite',
            'admin_setup_here' => 'Bitte aktualisiere <a href="/admin/settings"><strong>hier</strong></a> die Einstellungen um Inhalte hinzufügen zu können.',
            'pls_wait_for_admin_setup' => 'Es wurden bisher noch keine Inhalte hinzugefügt. Bitte habe noch etwas Gedult.',
            'loading_content' => 'Inhalte werden geladen, bitte warten.',
            'edit_user' => 'Benutzer bearbeiten',
            'next_episode' => 'Nächste Folge',
            'sorting' => 'Sortierung',
            'no_content_found' => 'Es wurden keine Inhalte gefunden.',
            'has_joined_the_chat' => 'ist dem Chat beigetreten.',
            'message' => 'Nachricht',
            'send' => 'Senden',
            'search' => 'Suche',
            'search_result' => 'Suchergebnisse für:',

			// Movie
			'rating' => 'Bewertung',
			'runtime' => 'Dauer',
			'release_date' => 'Erscheinungsdatum',
			
			'select_new_poster' => 'Wähle ein neues Poster',
			'select_new_thumbnail' => 'Wähle ein neues Thumbnail',
            'select_movie_file' => 'Wähle eine Video Datei aus',
			'add_movie' => 'Möchtest du den Film hinzufügen?',

            'apikey_info'=> 'Du brauchst einen eigenen API Key von TMDB um Inhalte laden zu können. Klicke <a href="https://www.themoviedb.org/settings/api" class="smaller" target="_blank" title="TMDB">hier</a> um einen Schlüssel zu bekommen!',
            'lang_info' => 'Dies wird benötigt um alle Inhalte in der gewünschten Sprache zu laden. Format: language-COUNTRY (Bsp.: de-DE)',
            'trailer_info' => 'ID der YouTube Video URL. Bsp.: https://www.youtube.com/watch?v=<strong>iUISLveqaII</strong>',
            'enable_edit_btn' => 'Möchtest du die Bearbeitung von Medien über die Hauptseiten erlauben? (Betrifft nur Admins)',

            // -- Messages --
            // Success
            'settings_update_success' => 'Einstellungen wurden erfolgreich aktualisiert!',
            'edit_user_success' => 'Der Benutzer wurde erfolgreich bearbeitet!',
            'add_user_success' => 'Der Benutzer wurde erfolgreich hinzugefügt!',
            'delete_user_success' => 'Der Benutzer wurde erfolgreich gelöscht!',

            'edit_movie_success' => 'Der Film wurde erfolgreich bearbeitet!',
            'add_movie_success' => 'Der Film wurde erfolgreich hinzugefügt!',
            'delete_movie_success' => 'Der Film wurde erfolgreich gelöscht!',

            'add_show_success' => 'Die Serie wurde erfolgreich hinzugefügt!',
            'edit_show_success' => 'Die Serie wurde erfolgreich bearbeitet!',
            'delete_show_success' => 'Die Serie wurde erfolgreich gelöscht!',
            'update_show_success' => 'Die Serie wurde erfolgreich aktualisiert!',

            'genres_created_success' => 'Genre wurden erfolgreich geladen!',
            'update_poster_success' => 'Poster wurde erfolgreich aktualisiert!',
            'update_backdrop_success' => 'Thumbnail wurde erfolgreich aktualisiert!',
            'update_file_path_success' => 'Dateipfad wurde erfolgreich aktualisiert!',
            'update_trailer_success' => 'Trailer wurde erfolgreich aktualisiert!',
            'update_show_success' => 'Serie wurde erfolgreich aktualisiert!',

            'add_highlight_success' => 'Highlight wurde erfolgreich hinzugefügt!',
            'delete_highlight_success' => 'Highlight wurde erfolgreich gelöscht!',
            'delete_season_success' => 'Staffel wurde erfolgreich gelöscht werden!',

            'logout_message' => 'Du hast dich erfolgreich abgemeldet!',
            'user_img_upload_success' => 'Bild erfolgreich hochgeladen!',
            'user_img_update_success' => 'Bild erfolgreich aktualisiert!',

            // Alert
			'failed_to_save' => 'Speichern fehlgeschlagen!',
            'edit_user_alert' => 'Der Benutzer konnte nicht bearbeitet werden!',
            'add_user_alert' => 'Der Benutzer konnte nicht hinzugefügt werden!',
            'delete_user_alert' => 'Der Benutzer konnte nicht gelöscht werden!',

            'edit_movie_alert' => 'Der Film konnte nicht bearbeitet werden!',
            'add_movie_alert' => 'Der Film konnte nicht hinzugefügt werden!',
            'delete_movie_alert' => 'Der Film konnte nicht gelöscht werden!',

            'add_show_alert' => 'Die Serie konnte nicht hinzugefügt werden!',
            'edit_show_alert' => 'Die Serie konnte nicht bearbeitet werden!',
            'delete_show_alert' => 'Die Serie konnte nicht gelöscht werden!',
            'update_show_alert' => 'Die Serie konnte nicht aktualisiert werden!',

            'genres_created_alert' => 'Genre konntent nicht geladen werden!',
            'update_poster_alert' => 'Poster konntent nicht aktualisiert werden!',
            'update_backdrop_alert' => 'Thumbnail konntent nicht aktualisiert werden!',
            'update_file_path_alert' => 'Dateipfad konntent nicht aktualisiert werden!',
            'update_trailer_alert' => 'Trailer konntent nicht aktualisiert werden!',
            'update_show_alert' => 'Serie konntent nicht aktualisiert werden!',

            'add_highlight_alert' => 'Highlight konnte nicht hinzugefügt werden!',
            'delete_highlight_alert' => 'Highlight konnte nicht gelöscht werden!',
            'delete_season_alert' => 'Staffel konnte nicht gelöscht werden!',

            'user_img_upload_alert' => 'Bild konnte hochgeladen werden!',

            'username_pw_wrong' => 'Benutzername oder Passwort falsch!',

            // Warnings
            'username_not_found' => 'Benutzername exisitert nicht!',
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