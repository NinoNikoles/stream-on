<?php 
include(ROOT_PATH.'/views/header.php');
$conn = dbConnect();
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2 marg-right-col4">
            <div class="col12">
                <h1><?php echo lang_snippet('users'); ?></h1>
            </div>

            <div class="col12 text-right">
                <button data-src="#add-user" class="btn btn-secondary icon-left icon-add-user" data-fancybox><?php echo lang_snippet('add_user'); ?></button>
            </div>

            <div class="col12">
                <?php                    
                    // Benutzer gelöscht
                    if(isset($_POST['delete-user'])) {
                        deleteUser($_POST['userID']);
                    }

                    // Benutzerregistrierung
                    if(isset($_POST['register'])) {
                        registerUser($_POST);
                    }

                    // Edit User
                    if(isset($_POST['edit-user'])) {
                        editUser($_POST);
                    }

                    // Messages
                    callout();
                ?>
            </div>

            <table>
                <thead>
                    <th><?php echo lang_snippet('username'); ?></th>
                    <th><?php echo lang_snippet('role'); ?></th>
                    <th><?php echo lang_snippet('edit'); ?></th>
                    <th><?php echo lang_snippet('delete'); ?></th>
                </thead>
                <?php

                    $sql = "SELECT id, username, role FROM users";
                    $results = $conn->query($sql);
                    while($row = $results->fetch_assoc()) {
                        
                        if (!($row['role'] === NULL) && ($row['role'] > 0)) {
                            $role = "Admin";
                        } else {
                            $role = "User";
                        }

                        echo '<tr>';
                            echo '<td>'.$row['username'].'</td>';
                            echo '<td>'.$role.'</td>';
                            echo '<td><button data-src="#edit-user-'.$row['id'].'" class="btn btn-success icon-only icon-pen marg-no" data-fancybox></button></td>';
                            echo '<td><button data-src="#delete-user-'.$row['id'].'" class="btn btn-alert icon-only icon-trash marg-no" data-fancybox></button></td>';
                        echo '</tr>';
                    }

                ?>
            </table>
            <?php
                $sql = "SELECT id, username, firstname, lastname, role FROM users";
                $results = $conn->query($sql);
                while($row = $results->fetch_assoc()) {
                    // Edit User Box
                    echo '<div id="edit-user-'.$row['id'].'" style="display:none;">';
                        echo '<p>Nutzer bearbeiten</p>';
                        echo '<form method="post" action="/admin/users">';
                            echo '<p>';
                                echo '<lable for="username-'.$row['id'].'">Benutzername <input type="text" id="username-'.$row['id'].'" name="username" value="'.$row['username'].'" required></lable>';
                                if (!($row['role'] === NULL) && ($row['role'] > 0)) {
                                    $checked = "checked";
                                } else {
                                    $checked = "";
                                }
                            echo '</p>';
                            echo '<p>';
                                echo '<lable for="role-'.$row['id'].'" class="checkbox-label">Admin <input type="checkbox" id="role-'.$row['id'].'" name="role" value="1" '.$checked.'></lable>';
                            echo '</p>';
                            echo '<p>';
                                echo '<lable for="password-'.$row['id'].'">Passwort <input type="password" id="password-'.$row['id'].'" name="password" required></lable>';
                                echo '<input type="number" name="userID" id="userID-'.$row['id'].'" value="'.$row['id'].'" style="display:none;" required>';
                            echo '</p>';
                            echo '<p class="text-right marg-no">';
                                echo '<button class="btn btn-success" type="submit" name="edit-user">Speichern</button>';
                            echo '</p>';
                        echo '</form>';
                    echo '</div>';
                    
                    // Delete User Box
                    echo '<div id="delete-user-'.$row['id'].'" style="display:none;">';
                        echo '<p>Möchtest du den Nutzer <strong>"'.$row['username'].'"</strong> wirklich löschen?</p>';
                        echo '<form method="post" action="/admin/users">';
                            echo '<p class="text-right marg-no">';
                                echo '<input type="number" name="userID" value="'.$row['id'].'" style="display:none;" required>';
                                echo '<button class="btn btn-alert" type="submit" name="delete-user">'.lang_snippet('delete').'</button>';
                            echo '</p>';
                        echo '</form>';
                    echo '</div>';
                }
            ?>

            <div id="add-user" style="display:none;">
                <h2 class="h4">Neuen Nutzer hinzufügen</h2>
                <form method="post" action="users">              
                    <p>
                        <label for="username">Benutzername
                        <input type="text" id="username" name="username" placeholder="Benutzername" required></label>
                    </p>
                    <p>
                        <lable for="role" class="checkbox-label">Admin <input type="checkbox" id="role" name="role"></lable>
                    </p>
                    <p>
                        <label for="password">Passwort
                        <input type="password" id="password" name="password" placeholder="Passwort" required></label>
                    </p>
                    <p class="text-right">
                        <button class="btn btn-success" type="submit" name="register">Registrieren</button>
                    </p>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>