<?php
$pageTitle = pageTitle(lang_snippet(('users')));
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
                <button data-src="#add-user" class="btn btn-small btn-success icon-left icon-add-user" data-fancybox><?php echo lang_snippet('add_user'); ?></button>
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
                    <th class="desktop-only"><?php echo lang_snippet('user_img'); ?></th>
                    <th><?php echo lang_snippet('username'); ?></th>
                    <th class="desktop-only"><?php echo lang_snippet('role'); ?></th>
                    <th><?php echo lang_snippet('edit'); ?></th>
                    <th><?php echo lang_snippet('password'); ?></th>
                    <th><?php echo lang_snippet('delete'); ?></th>
                </thead>
                <?php
                    $sql = "SELECT id, username, user_img, role FROM users";
                    $results = $conn->query($sql);
                    while($row = $results->fetch_assoc()) {
                        
                        if ( $row['role'] === 'superadmin' ) {
                            $role = "Super Admin";
                        } else if ( $row['role'] === 'admin' ) {
                            $role = "Admin";
                        } else {
                            $role = "User";
                        }

                        echo '<tr>';
                            echo '<td class="desktop-only"><figure class="square"><img data-img="'.uploadedIMG($row['username'], $row['user_img']).'" loading="lazy" alt=""></figure></td>';
                            echo '<td>'.$row['username'].'</td>';
                            echo '<td class="desktop-only">'.$role.'</td>';

                            if ( $row['role'] === 'superadmin' && $_SESSION['role'] === 'superadmin' ) {
                                echo '<td><button data-src="#edit-user-'.$row['id'].'" title="'.lang_snippet('edit').'" class="btn btn-small btn-warning icon-only icon-pen marg-no" data-fancybox></button></td>';
                                echo '<td><button data-src="#pw-user-'.$row['id'].'" title="'.lang_snippet('change_password').'" class="btn btn-small btn-warning icon-only icon-key marg-no" data-fancybox></button></td>';
                            } else if ( $row['role'] === 'admin' && $_SESSION['role'] !== 'user' || $row['role'] === 'user' && $_SESSION['role'] !== 'user' ) {
                                echo '<td><button data-src="#edit-user-'.$row['id'].'" title="'.lang_snippet('edit').'" class="btn btn-small btn-warning icon-only icon-pen marg-no" data-fancybox></button></td>';
                                echo '<td><button data-src="#pw-user-'.$row['id'].'" title="'.lang_snippet('change_password').'" class="btn btn-small btn-warning icon-only icon-key marg-no" data-fancybox></button></td>';
                            } else {
                                echo '<td></td>';
                                echo '<td></td>';
                            }
                            

                            if ( $row['role'] !== 'superadmin' ) {
                                echo '<td><button data-src="#delete-user-'.$row['id'].'" title="'.lang_snippet('delete').'" class="btn btn-small btn-alert icon-only icon-trash marg-no" data-fancybox></button></td>';
                            } else {
                                echo '<td></td>';
                            }
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
                        echo '<h2 class="h4">'.lang_snippet('edit_user').'</h2>';
                        echo '<form method="post" action="/admin/users">';
                            echo '<p>';
                                echo '<lable for="username-'.$row['id'].'">'.lang_snippet('username').' <input type="text" id="username-'.$row['id'].'" name="username" value="'.$row['username'].'" required></lable>';
                            echo '</p>';
                            
                            if ( $row['role'] === 'superadmin' ) {
                                echo '<p style="display:none;">';
                                    echo '<lable for="role-'.$row['id'].'" class="checkbox-label">Super '.lang_snippet('admin').' <input type="checkbox" id="role-'.$row['id'].'" name="role" value="superadmin" checked></lable>';
                                echo '</p>';
                            } else if ( $row['role'] === 'admin' ) {
                                echo '<p>';
                                    echo '<lable for="role-'.$row['id'].'" class="checkbox-label">'.lang_snippet('admin').' <input type="checkbox" id="role-'.$row['id'].'" name="role" value="admin" checked></lable>';
                                echo '</p>';
                            } else {
                                echo '<p>';
                                    echo '<lable for="role-'.$row['id'].'" class="checkbox-label">'.lang_snippet('admin').' <input type="checkbox" id="role-'.$row['id'].'" name="role" value="admin"></lable>';
                                echo '</p>';
                            } 
                            
                            echo '<p>';
                                echo '<input type="number" name="userID" id="userID-'.$row['id'].'" value="'.$row['id'].'" style="display:none;" required>';
                            echo '</p>';
                            echo '<p class="text-right marg-no">';
                                echo '<button class="btn btn-success icon-left icon-save" title="'.lang_snippet('save').'" type="submit" name="edit-user">'.lang_snippet('save').'</button>';
                            echo '</p>';
                        echo '</form>';
                    echo '</div>';

                    // Change User Password Box
                    echo '<div id="pw-user-'.$row['id'].'" style="display:none;">';
                        echo '<h2 class="h4">'.lang_snippet('edit_user').'</h2>';
                        echo '<form>';
                            echo '<p>';
                                echo '<lable for="password-'.$row['id'].'">'.lang_snippet('password').' <input type="password" id="password-'.$row['id'].'" class="password-'.$row['id'].'" name="password" required></lable>';
                                echo '<input type="text" id="username-'.$row['id'].'" name="username" value="'.$row['username'].'" style="display:none;" required>';
                                echo '<input type="number" name="userID" id="userID-'.$row['id'].'" value="'.$row['id'].'" style="display:none;" required>';
                            echo '</p>';
                            echo '<p>';
                                echo '<lable for="password-check-'.$row['id'].'">'.lang_snippet('password').' <input type="password" disabled id="password-'.$row['id'].'-check" class="password-'.$row['id'].'-check" name="password-check" required></lable>';
                            echo '</p>';
                            echo '<p class="text-right marg-no">';
                                echo '<button disabled class="btn btn-success icon-left icon-save disabled change-user-pw" value="'.$row['id'].'" title="'.lang_snippet('save').'" type="submit" name="change-user-pw">'.lang_snippet('save').'</button>';
                            echo '</p>';
                        echo '</form>';
                    echo '</div>';
                    
                    // Delete User Box
                    echo '<div id="delete-user-'.$row['id'].'" style="display:none;">';
                        echo '<p>Möchtest du den Nutzer <strong>"'.$row['username'].'"</strong> wirklich löschen?</p>';
                        echo '<form method="post" action="/admin/users">';
                            echo '<p class="text-right marg-no">';
                                echo '<input type="number" name="userID" value="'.$row['id'].'" style="display:none;" required>';
                                echo '<button class="btn btn-alert icon-left icon-trash" title="'.lang_snippet('delete').'" type="submit" name="delete-user">'.lang_snippet('delete').'</button>';
                            echo '</p>';
                        echo '</form>';
                    echo '</div>';
                }
            ?>

            <div id="add-user" style="display:none;">
                <h2 class="h4"><?php echo lang_snippet('add_user'); ?></h2>
                <form method="post" action="users">              
                    <p>
                        <label for="username"><?php echo lang_snippet('username'); ?>
                        <input type="text" id="username" name="username" placeholder="<?php echo lang_snippet('username'); ?>" required></label>
                    </p>
                    <p>
                        <lable for="role" class="checkbox-label"><?php echo lang_snippet('admin'); ?> <input type="checkbox" id="role" name="role"></lable>
                    </p>
                    <p>
                        <label for="password"><?php echo lang_snippet('password'); ?>
                        <input type="password" id="password" name="password" placeholder="<?php echo lang_snippet('password'); ?>" required></label>
                    </p>
                    <p class="text-right">
                        <button class="btn btn-success" title="<?php echo lang_snippet('save'); ?>" type="submit" name="register"><?php echo lang_snippet('save'); ?></button>
                    </p>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>