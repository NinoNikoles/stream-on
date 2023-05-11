<?php 
    include(ROOT_PATH.'/views/head.php');
    include(ROOT_PATH.'/views/header.php');

    $sql = 'SELECT id, username, user_img FROM users WHERE session="'.$_COOKIE['session_id'].'"';
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
      
    // output data of each row
        while($row = $result->fetch_assoc()) {
            if ($_GET['id'] !== $row['id']) {
                header('Location: /404');
            }
            if ($row['user_img'] === NULL || $row['user_img'] === '') {
                $currentUserIMG = '/build/css/images/placeholder.webp';
            } else {
                $currentUserIMG = '/uploads/' . $row['user_img'];
            }
            
            $currentUsername = $row['username'];
        }
    } else {
        header('Location: /404');
    }

    $conn = $mysqli;

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
                        set_callout('success','user img upload success');
                        header('Location: /user/?id='.$_POST['id']);
                        exit();
                    } else {
                        set_callout('alert','user img upload alert');
                        header('Location: /user/?id='.$_POST['id']);
                        exit();
                    }
                } else {
                    echo 'Das hochgeladene File muss ein Bild sein (JPG, JPEG, PNG, GIF).';
                    set_callout('warning','user img upload wrong file');
                    header('Location: /user/?id='.$_POST['id']);
                    exit();
                }
            } else {
                set_callout('warning','user img upload no file');
                header('Location: /user/?id='.$_POST['id']);
                exit();
            }
        }
?>

<div class="col12">
    <div class="innerWrap">
        
        <div class="col8 marg-top-xxl marg-left-col2 marg-right-col4">

            <div class="col4 marg-left-col4 marg-right-col4">
                <h1 class="text-center"><?php echo $currentUsername; ?></h1>
                <figure class="square">
                    <img src="<?php echo $currentUserIMG; ?>">
                </figure>


                <?php callout(); ?>


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