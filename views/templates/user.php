<?php 
    $pageTitle = pageTitle(lang_snippet(('profile')));
    include(ROOT_PATH.'/views/header.php');

    userCheck();

    $conn = dbConnect();
?>

<div class="col12">
    <div class="innerWrap marg-top-xxl">
        
        <div class="col8 marg-left-col2">
            <?php callout(); ?>
        </div>

        <div class="col8 marg-left-col2">
            <div class="col5 marg-right-col1">
                <figure class="square">
                    <img data-img="<?php echo userProfileImg(); ?>" id="user-img" loading="lazy" alt="">
                </figure>                
            </div>
            <div class="col6 pad-top-xs">
                <h1><?php echo $_SESSION['username']; ?></h1>

                <form>
                    <input type="number" name="id" value="<?php echo $_GET['id']; ?>" style="display:none;">
                    <p>
                        <lable for="user-img"><?php echo lang_snippet('upload_new_img'); ?>
                            <input type="file" name="user-img" id="userImgInput" accept="image/*">
                        </lable>
                    </p>
                    <p class="text-right">
                        <button id="userImgUpload" type="submit" class="btn btn-small btn-success loading" name="submit" value="Hochladen">Hochladen</button>
                    </p>
                </form>
            </div>
        </div>

        <?php
            $images = false;
            $currentImg = false;
            $sql = "SELECT user_img, uploads FROM users WHERE id=".$_SESSION['userID'].";";
            $result = $conn->query($sql);

            if ( $result->num_rows > 0 ) {
                while ( $resultImages = $result->fetch_assoc() ) {
                    if ( !($resultImages['uploads'] === NULL) ) {
                        $images = array_reverse(json_decode($resultImages['uploads']));
                    }

                    if ( !($resultImages['user_img'] === NULL) ) {
                        $currentImg = $resultImages['user_img'];
                    }
                }
                
                if ( $images ) {
                    $i = 0;
                    ?>
                        <div id="uploads" class="col8 marg-top-xl marg-bottom-xl marg-left-col2">
                            <div class="col12">
                                <h2 class="h3"><?php echo lang_snippet('all_uploads'); ?></h2>
                            </div>

                            <div class="col10">
                                <div class="col12 grid-row" id="allUserUploads">
                                    <?php
                                        foreach ( $images as $image ) {
                                            if ( !($image === $currentImg) ) {
                                                echo '<div class="col-6 col-3-xsmall col-2-medium grid-padding marg-bottom-s select-item">';
                                                    echo '<div class="user-img-select">';
                                                        echo '<input type="radio" id="img-'.$i.'" name="userImg" value="'.$image.'" data-current="0" data-id="'.$_SESSION['userID'].'">';
                                                        echo '<figure class="square">';
                                                            echo '<img data-img="'.uploadedIMG($_SESSION['username'], $image).'" loading="lazy" alt="">';
                                                        echo '</figure>';
                                                    echo '</div>';
                                                echo '</div>';
                                                $i++;
                                            } else {
                                                echo '<div class="col-6 col-3-xsmall col-2-medium grid-padding marg-bottom-s select-item">';
                                                    echo '<div class="user-img-select">';
                                                        echo '<input type="radio" id="img-'.$i.'" name="userImg" value="'.$image.'" data-current="1" data-id="'.$_SESSION['userID'].'" checked>';
                                                        echo '<figure class="square">';
                                                            echo '<img data-img="'.uploadedIMG($_SESSION['username'], $image).'" loading="lazy" alt="">';
                                                        echo '</figure>';
                                                    echo '</div>';
                                                echo '</div>';
                                                $i++;
                                            }
                                        }
                                    ?>
                                </div>
                            </div>

                            <div class="col2 text-right">
                                <a href="#" class="btn btn-small btn-success icon-left icon-save loading" id="updateUserImg" style="display:none"><?php echo lang_snippet('save'); ?></a>
                                <a href="#" class="btn btn-small btn-alert icon-left icon-trash loading marg-no" id="deleteUserImg"><?php echo lang_snippet('delete'); ?></a>
                            </div>
                        </div>
                    <?php
                }
            }
        ?>
    </div>
</div>

<?php include('views/footer.php'); ?>