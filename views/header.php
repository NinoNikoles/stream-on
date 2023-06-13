<?php
$theme = setTheme();
$conn = dbConnect();
?>
<!DOCTYPE html>
<html lang="<?php echo get_browser_language(); ?>" <?php echo $theme;?> >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="">
    <link rel="stylesheet" type="text/css" href="/views/build/style.min.css">
    <link rel="stylesheet" type="text/css" href="/views/build/font.min.css">
    <title>Vite App</title>
</head>
<body>
<header id="header" class="bar-active-root bar-active fixed-header overlay" lang="de-DE">
	<div class="row header--content">
		<div class="col12 column header--content--nav">

			<!-- Logo -->
			<div class="header--logo">
				<a class="logo--small" title="Zur Startseite" href="/">
                    <span class="bold">Framework</span>
                </a>
            </div>

			<!-- Hauptnavigation -->
			<div class="navWrap">
				
				<!-- Navigation -->
				<nav id="navMain" class="header-menu-main" style="top: 50px; height: calc(100vh - 50px);">
                    <ul class="menu">
                        <?php echo adminMenu();?>
                        
                        <li class="menu-item mobile-only"><a href="/logout" title="<?php echo lang_snippet('logout'); ?>"><?php echo lang_snippet('logout'); ?></a></li>
                    </ul>
                </nav>
                
                <?php 
                    $sql = 'SELECT id, user_img FROM users WHERE username="'.$_SESSION['username'].'"';
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                     
                        // output data of each row
                        while($row = $result->fetch_assoc()) {
                            
                            if ($row['user_img'] === NULL || $row['user_img'] === '') {
                                $userProfileImg = '/views/build/css/images/placeholder.webp';
                            } else {
                                $userProfileImg = '/uploads/'.$row['user_img'];
                            }

                            $currentUserID = $row['id'];
                        }
                    } else {
                        page_redirect("/logout");
                    }
                ?>
                <!-- Profil -->
                <a href="/user/?id=<?php echo $currentUserID; ?>" id="user-profile">
                    <figure class="square">
                        <img src="<?php echo $userProfileImg; ?>">
                    </figure>
                </a>

                <!-- Theme Switch button -->
                <a href="#" id="theme-switch" class="icon"></a>

                <!-- Mobile Menu Button -->
                <a href="#" class="nav-trigger menu-button" title="Menü öffnen">
                    <span class="tx">MENÜ</span>
                    <span class="trigger-bar bar-1"></span>
                    <span class="trigger-bar bar-2"></span>
                    <span class="trigger-bar bar-3"></span>
                </a>
			</div>				

						
		</div>
	</div>

</header>