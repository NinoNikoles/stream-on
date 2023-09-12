<?php
$conn = dbConnect();

$newImg = $_POST['img'];
$userID = $_POST['userID'];

// Überprüfen, ob ein Bild ausgewählt wurde
if(isset($_FILES['file'])) {
    // Bildinformationen aus dem $_FILES-Array extrahieren
    $bildName = $_FILES['file']['name'];
    $bildTmpName = $_FILES['file']['tmp_name'];
    $bildSize = $_FILES['file']['size'];
    $bildError = $_FILES['file']['error'];
    $bildType = $_FILES['file']['type'];

    // Dateiendung des Bildes extrahieren
    $bildExt = strtolower(pathinfo($bildName, PATHINFO_EXTENSION));

    // Erlaubte Dateitypen festlegen
    $erlaubteTypen = array('jpg', 'jpeg', 'png', 'gif', 'svg');

    $uploadDir = ROOT_PATH.'/uploads/'.userNameStringFormatter($_SESSION['username']);

    if ( !is_dir($uploadDir) ) {
        mkdir($uploadDir, 0777, true);
    }

    // Überprüfen, ob die Datei ein Bild ist und erlaubte Dateitypen hat
    if(in_array($bildExt, $erlaubteTypen)) {
        // Dateinamen für das Bild generieren
        $neuerName = uniqid('', true) . '.' . $bildExt;
        $ziel = $uploadDir.'/'. $neuerName;

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
            set_callout('success',lang_snippet('user_img_upload_success'));
        } else {
            set_callout('alert',lang_snippet('user_img_upload_alert'));
        }
    } else {
        //echo 'Das hochgeladene File muss ein Bild sein (JPG, JPEG, PNG, GIF).';
        set_callout('warning',lang_snippet('user_img_upload_wrong_file'));
    }
} else {
    set_callout('warning',lang_snippet('user_img_upload_no_file'));
}