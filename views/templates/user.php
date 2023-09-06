<?php 
    $pageTitle = pageTitle(lang_snippet(('profile')));
    include(ROOT_PATH.'/views/header.php');

    userCheck();

    $conn = dbConnect();

        // Überprüfen, ob das Formular abgeschickt wurde
        if(isset($_POST['submit'])) {
            // Überprüfen, ob ein Bild ausgewählt wurde
            if(isset($_FILES['user-img'])) {
                // Bildinformationen aus dem $_FILES-Array extrahieren
                $bildName = $_FILES['user-img']['name'];
                $bildTmpName = $_FILES['user-img']['tmp_name'];
                $bildSize = $_FILES['user-img']['size'];
                $bildError = $_FILES['user-img']['error'];
                $bildType = $_FILES['user-img']['type'];
    
                // Dateiendung des Bildes extrahieren
                $bildExt = strtolower(pathinfo($bildName, PATHINFO_EXTENSION));
    
                // Erlaubte Dateitypen festlegen
                $erlaubteTypen = array('jpg', 'jpeg', 'png', 'gif', 'svg');
    
                // Überprüfen, ob die Datei ein Bild ist und erlaubte Dateitypen hat
                if(in_array($bildExt, $erlaubteTypen)) {
                    // Dateinamen für das Bild generieren
                    $neuerName = uniqid('', true) . '.' . $bildExt;
                    $ziel = ROOT_PATH.'/uploads/' . $neuerName;
        
                    // Bild in den Zielordner verschieben
                    move_uploaded_file($bildTmpName, $ziel);

                    $imgArray = [];

                    $sql = "SELECT uploads FROM users WHERE id=".$_SESSION['userID'].";";
                    $result = $conn->query($sql);
                    while ( $resultImages = $result->fetch_assoc() ) {
                        $images = json_decode($resultImages['uploads']);
                    }

                    if ( $images ) {
                        foreach ( $images as $image ) {
                            $imgArray[] = $image;
                        }
                    }

                    $imgArray[] = $neuerName;
                    $imgArray = json_encode($imgArray);
                    $sql = "UPDATE users SET uploads='$imgArray' WHERE id=".$_SESSION['userID'].";";
                    if ( !($conn->query($sql) === TRUE) ) {
                        set_callout('alert','user_img_upload_alert');
                        page_redirect("/user/?id=".$_POST['id']);
                    }

                    $sql = "UPDATE users SET user_img='$neuerName' WHERE id=".$_SESSION['userID'].";";
                    if ($conn->query($sql) === TRUE) {
                        set_callout('success','user_img_upload_success');
                        page_redirect("/user/?id=".$_POST['id']);
                    } else {
                        set_callout('alert','user_img_upload_alert');
                        page_redirect("/user/?id=".$_POST['id']);
                    }
                } else {
                    echo 'Das hochgeladene File muss ein Bild sein (JPG, JPEG, PNG, GIF).';
                    set_callout('warning','user_img_upload_wrong_file');
                    page_redirect("/user/?id=".$_POST['id']);
                }
            } else {
                set_callout('warning','user_img_upload_no_file');
                page_redirect("/user/?id=".$_POST['id']);
            }
        }
?>

<div class="col12">
    <div class="innerWrap marg-top-xxl">
        
        <div class="col8 marg-left-col2">
            <?php callout(); ?>
        </div>

        <div class="col8 marg-left-col2">
            <div class="col5 marg-right-col1">
                <figure class="square">
                    <img data-img="<?php echo userProfileImg(); ?>" loading="lazy" alt="">
                </figure>                
            </div>
            <div class="col6 pad-top-xs">
                <h1><?php echo $_SESSION['username']; ?></h1>

                <form action="/user/?id=<?php echo $_GET['id']; ?>" method="POST" enctype="multipart/form-data">
                    <input type="number" name="id" value="<?php echo $_GET['id']; ?>" style="display:none;">
                    <p>
                        <lable for="user-img">User img
                            <input type="file" name="user-img" accept="image/*">
                        </lable>
                    </p>
                    <p class="text-right">
                        <button type="submit" class="btn btn-small btn-success" name="submit" value="Hochladen">Hochladen</button>
                    </p>
                </form>
            </div>
        </div>

        <?php
        $sql = "SELECT user_img, uploads FROM users WHERE id=".$_SESSION['userID'].";";
        $result = $conn->query($sql);
        while ( $resultImages = $result->fetch_assoc() ) {
            $images = array_reverse(json_decode($resultImages['uploads']));
            $currentImg = $resultImages['user_img'];
        }
        
        if ( $images) {
            $i = 0;
        ?>

        <div class="col8 marg-top-xl marg-bottom-xl marg-left-col2">
            <div class="col12">
                <h2 class="h3"><?php echo lang_snippet('all_uploads'); ?></h2>
            </div>

            <div class="col12 grid-row">
                <?php
                    foreach ( $images as $image ) {
                        if ( !($image === $currentImg) ) {
                            echo '<div class="col-6 col-3-xsmall col-2-medium grid-padding marg-bottom-s">';
                                echo '<div class="user-img-select">';
                                    echo '<input type="radio" id="img-'.$i.'" name="userImg" value="'.$image.'" data-current="0" data-id="'.$_SESSION['userID'].'">';
                                    echo '<figure class="square">';
                                        echo '<img data-img="'.uploadedIMG($image).'" loading="lazy" alt="">';
                                    echo '</figure>';
                                echo '</div>';
                            echo '</div>';
                            $i++;
                        } else {
                            echo '<div class="col-6 col-3-xsmall col-2-medium grid-padding marg-bottom-s">';
                                echo '<div class="user-img-select">';
                                    echo '<input type="radio" id="img-'.$i.'" name="userImg" value="'.$image.'" data-current="1" data-id="'.$_SESSION['userID'].'" checked>';
                                    echo '<figure class="square">';
                                        echo '<img data-img="'.uploadedIMG($image).'" loading="lazy" alt="">';
                                    echo '</figure>';
                                echo '</div>';
                            echo '</div>';
                            $i++;
                        }
                    }
                ?>
            </div>

            <div class="col12 text-right">
                <a href="#" class="btn btn-small btn-success marg-no" id="updateUserImg" style="display:none"><?php echo lang_snippet('save'); ?></a>
            </div>
        </div>

        <?php } ?>
    </div>
</div>

<?php include('views/footer.php'); ?>