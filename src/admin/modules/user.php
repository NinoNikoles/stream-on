<?php
function getUserID() {
    return $_SESSION['userID'];
}

function userCheck() {
    $conn = dbConnect();
    $sessionUserID = $_SESSION['userID'];

    $sql = "SELECT id, username, user_img FROM users WHERE id='$sessionUserID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        // output data of each row
        while($row = $result->fetch_assoc()) {
            if ( $_GET['id'] !== $_SESSION['userID'] || $_GET['id'] !== $row['id'] || $row['username'] != $_SESSION['username'] ) {
                page_redirect("/404");
            }
        }
    } else {
        page_redirect("/404");
    }
}

function userProfileImg() {
    $conn = dbConnect();
    $sessionUserID = $_SESSION['userID'];
    $sql = "SELECT user_img FROM users WHERE id='$sessionUserID'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
     
        // output data of each row
        while($row = $result->fetch_assoc()) {
            
            if ($row['user_img'] === NULL || $row['user_img'] === '') {
                $userProfileImg = '/views/build/css/images/placeholder.webp';
            } else {
                $userProfileImg = '/uploads/'.$row['user_img'];
            }
        }
    } else {
        $userProfileImg = '/views/build/css/images/placeholder.webp';
    }

    return $userProfileImg;
}

function userProfileImgByID($userID) {
    $conn = dbConnect();
    $sql = "SELECT user_img FROM users WHERE id=$userID;";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
     
        // output data of each row
        while($row = $result->fetch_assoc()) {
            
            if ($row['user_img'] === NULL || $row['user_img'] === '') {
                $userProfileImg = '/views/build/css/images/placeholder.webp';
            } else {
                $userProfileImg = '/uploads/'.$row['user_img'];
            }
        }
    } else {
        $userProfileImg = '/views/build/css/images/placeholder.webp';
    }

    return $userProfileImg;
}

function uploadedIMG($uploadedImg) {
    $img = '/uploads/'.$uploadedImg;
    return $img;
}

function registerUser($post) {
    $conn = dbConnect();
    $username = mysqli_real_escape_string($conn, $post['username']);
    if (isset($post['role']) && $_POST['role'] === 'on') {
        $role = mysqli_real_escape_string($conn, 'admin');
    } else {
        $role = mysqli_real_escape_string($conn, 'user');
    }
    $password = mysqli_real_escape_string($conn, $post['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, role, password) VALUES ('$username', '$role', '$hashed_password')";
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','add_user_alert');
        page_redirect("/admin/users");
    } else {
        set_callout('success','add_user_success');
        page_redirect("/admin/users");
    }
}

function editUser($post) {
    $conn = dbConnect();
    $userID = mysqli_real_escape_string($conn, $post['userID']);
    $username = mysqli_real_escape_string($conn, $post['username']);
    if (isset($post['role'])) {
        $role = mysqli_real_escape_string($conn, $post['role']);
    } else {
        $role = 'user';
    }
    $password = mysqli_real_escape_string($conn, $post['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $sql = "UPDATE users SET username='$username', password='$hashed_password', role='$role' WHERE id='$userID'";

    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','delete_user_alert');
        page_redirect("/admin/users");
    } else {
        set_callout('success','edit_user_success');
        if ( intval($_SESSION['userID']) === intval($post['userID']) ) {
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $role;
            $_SESSION['logged_in'] = true;
        }

        page_redirect('/admin/users');
    }
}

function deleteUser($userID) {
    $conn = dbConnect();
    $sql = "DELETE FROM users WHERE id='$userID'";
    if (!($conn->query($sql) === TRUE)) {
        set_callout('alert','delete_user_alert');
        page_redirect("/admin/users");
    } else {
        set_callout('success','delete_user_success');
        page_redirect("/admin/users");
    }
}
?>