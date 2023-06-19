<?php 
include(ROOT_PATH.'/views/header.php');
$conn = dbConnect();
?>

<div class="col12">

    <?php get_backend_menu(); ?>

    <div class="innerWrap">
        
        <div class="col8 marg-top-xxl marg-left-col2 marg-right-col4">
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
                        $userID = mysqli_real_escape_string($conn, $_POST['userID']);
                        $sql = 'DELETE FROM users WHERE id="'.$userID.'"';
                        if (!($conn->query($sql) === TRUE)) {
                            set_callout('alert','delete_user_alert');
                            page_redirect("/admin/users");
                        } else {
                            set_callout('success','delete_user_success');
                            page_redirect("/admin/users");
                        }
                    }

                    // Benutzerregistrierung
                    if(isset($_POST['register'])) {
                        $username = mysqli_real_escape_string($conn, $_POST['username']);
                        if (isset($_POST['role']) && $_POST['role'] === 'on') {
                            $role = mysqli_real_escape_string($conn, 1);
                        } else {
                            $role = mysqli_real_escape_string($conn, 0);;
                        }
                        $password = mysqli_real_escape_string($conn, $_POST['password']);
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                        
                        $sql = "INSERT INTO users (username, role, password) VALUES ('$username', '$role', '$hashed_password')";
                        if (!($conn->query($sql) === TRUE)) {
                            set_callout('alert','add_user_alert');
                            page_redirect("/admin/users");
                        } else {
                            set_callout('success','add_user_success');
                            page_redirect("/adminusers");
                        }
                    }

                    // Edit User
                    if(isset($_POST['edit-user'])) {
                        $userID = mysqli_real_escape_string($conn, $_POST['userID']);
                        $username = mysqli_real_escape_string($conn, $_POST['username']);
                        if (isset($_POST['role'])) {
                            $role = mysqli_real_escape_string($conn, $_POST['role']);
                        } else {
                            $role = 0;
                        }
                        $password = mysqli_real_escape_string($conn, $_POST['password']);
                        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                        $sql = 'UPDATE users SET username="'.$username.'", password="'.$hashed_password.'", role="'.$role.'" WHERE id="'.$userID.'"';

                        if (!($conn->query($sql) === TRUE)) {
                            set_callout('alert','delete_user_alert');
                            page_redirect("/admin/users");
                        } else {
                            set_callout('success','edit_user_success');
                            session_start();
                            $_SESSION['username'] = $username;
                            $_SESSION['role'] = $role;
                            $_SESSION['logged_in'] = true;
                
                            page_redirect('/admin/users');
                        }
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
                        echo '<form method="post" action="users">';
                            echo '<p>';
                                echo '<lable for="username">Benutzername <input type="text" name="username" value="'.$row['username'].'" required></lable>';
                                if (!($row['role'] === NULL) && ($row['role'] > 0)) {
                                    $checked = "checked";
                                } else {
                                    $checked = "";
                                }
                            echo '</p>';
                            echo '<p>';
                                echo '<lable for="role" class="checkbox-label">Admin <input type="checkbox" name="role" value="1" '.$checked.'></lable>';
                            echo '</p>';
                            echo '<p>';
                                echo '<lable for="password">Passwort <input type="password" name="password" required></lable>';
                                echo '<input type="number" name="userID" value="'.$row['id'].'" style="display:none;" required>';
                            echo '</p>';
                            echo '<p class="text-right marg-no">';
                                echo '<button class="btn btn-success" type="submit" name="edit-user">Speichern</button>';
                            echo '</p>';
                        echo '</form>';
                    echo '</div>';
                    
                    // Delete User Box
                    echo '<div id="delete-user-'.$row['id'].'" style="display:none;">';
                        echo '<p>Möchtest du den Nutzer <strong>"'.$row['username'].'"</strong> wirklich löschen?</p>';
                        echo '<form method="post" action="users">';
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
                        <input type="text" name="username" placeholder="Benutzername" required></label>
                    </p>
                    <p>
                        <lable for="role" class="checkbox-label">Admin <input type="checkbox" name="role"></lable>
                    </p>
                    <p>
                        <label for="password">Passwort
                        <input type="password" name="password" placeholder="Passwort" required></label>
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