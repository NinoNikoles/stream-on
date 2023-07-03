<?php 
    include(ROOT_PATH.'/views/head.php');
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

                    $sql = 'UPDATE users SET user_img="'.$neuerName.'" WHERE id="'.$_POST['id'].'"';
                    if ($conn->query($sql) === TRUE) {
                        echo "Record updated successfully";
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
    <div class="innerWrap marg-top-l">
        <?php callout(); ?>

        <div class="col8 marg-left-col2 marg-right-col4">
            <div class="col4 marg-left-col4 marg-right-col4">
                <h1 class="text-center"><?php echo $_SESSION['username']; ?></h1>
                <figure class="square">
                    <img src="<?php echo userProfileImg(); ?>" loading="lazy">
                </figure>


                


                <form action="/user/?id=<?php echo $_GET['id']; ?>" method="POST" enctype="multipart/form-data">
                    <input type="number" name="id" value="<?php echo $_GET['id']; ?>" style="display:none;">
                    <p>
                        <lable for="user-img">User img <input type="file" name="user-img" accept="image/*"></lable>
                    </p>
                    <p>
                        <button type="submit" name="submit" value="Hochladen">Hochladen</button>
                    </p>
                </form>
            </div>
        </div>

    </div>
</div>

<?php include('views/footer.php'); ?>