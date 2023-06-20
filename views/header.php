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
    <title><?php echo getSiteTitle();?></title>
</head>
<body>
<header id="header" class="bar-active-root bar-active fixed-header overlay" lang="de-DE">
	<div class="row header--content">
		<div class="col12 column header--content--nav">

			<!-- Logo -->
			<div class="header--logo">
				<a class="logo--small" title="Zur Startseite" href="/">
                    <span class="bold"><?php echo getSiteTitle();?></span>
                </a>
            </div>

			<!-- Hauptnavigation -->
			<div class="navWrap">

                <!-- Suche -->
                <div class="search-bar">
                    <form method="post" action="/search">
                        <div class="search-bar-fix"></div>
                        <input type="text" id="movie-live-search" name="search" placeholder="Suchen">
                        <button type="submit" class="search-btn"></button>
                    </form>
                    <div id="movieLivesearchResults"></div>
                </div>

				<!-- Navigation -->
				<nav id="navMain" class="header-menu-main" style="top: 50px; height: calc(100vh - 50px);">
                    <ul class="menu">
                        <li class="menu-item"><a href="/movies" title="<?php echo lang_snippet('movies'); ?>"><?php echo lang_snippet('movies'); ?></a></li>
                    </ul>
                </nav>

                <!-- Profil -->
                <button href="#" id="user-menu-btn">
                    <figure class="square">
                        <img src="<?php echo userProfileImg(); ?>">
                    </figure>

                    <menu class="user-menu">
                        <ul>
                            <?php echo adminMenu();?>
                            
                            <li class="menu-item"><a href="/user/?id=<?php echo getUserID(); ?>" title="<?php echo lang_snippet('profile'); ?>"><?php echo lang_snippet('profile'); ?></a></li>
                            <li class="menu-item"><a href="/logout" title="<?php echo lang_snippet('logout'); ?>"><?php echo lang_snippet('logout'); ?></a></li>
                        </ul>
                    </menu>
                </button>

                <!-- Theme Switch button -->
                <!--<a href="#" id="theme-switch" class="icon"></a>-->

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