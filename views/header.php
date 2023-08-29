<?php $conn = dbConnect(); 
    if( $_SESSION > 0 && !isset($_SESSION['logged_in']) || $_SESSION > 0 && $_SESSION['logged_in'] !== true ) {
        destroySesssion();
        if ( !pageCheck("/login") ) {
            page_redirect("/login");
        }
    }

    $siteTitle = getSiteTitle();

    if (!empty($_GET['remotesessionID'])) {
        $remotesessionID = $_GET['remotesessionID'];
    }
?>
<!DOCTYPE html>
<html lang="<?php echo get_browser_language(); ?>" data-theme="dark" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Enjoy your local movies and shows with a nice look">
    <?php loadFavicon(); ?>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.3.15/themes/default/style.min.css"/>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Swiper/10.0.4/swiper-bundle.min.css" integrity="sha512-Chxzu1hIgdTWzvBYD6rifXuhsG91mXCtYP/hKHSHPRyYUluMJt3PVEE6k4ThFrE4l0NVnq9dfYSj7mEubGCTvw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css">
    <link rel="stylesheet" href="/views/build/style.min.css" type="text/css" media="screen">
    <link rel="stylesheet" href="/views/build/font.min.css" type="text/css" media="screen">
    <title><?php echo $siteTitle; ?></title>
    <script src="https://www.youtube.com/iframe_api"></script>
</head>
<body class="loading">

<div id="loader" class="visible">
    <div class="content-wrap">
        <i></i>
        <span><?php echo lang_snippet('loading_content'); ?></span>
    </div>
</div>

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
                <!-- <div class="search-bar">
                    <form method="post" action="/search">
                        <div class="search-bar-fix"></div>
                        <input type="text" id="movie-live-search" name="search" placeholder="Suchen">
                        <button type="submit" class="search-btn"></button>
                    </form>
                    <div id="movieLivesearchResults"></div>
                </div>-->

				<!-- Navigation -->
				<nav id="navMain" class="header-menu-main" style="top: 50px; height: calc(100vh - 50px);">
                    <ul class="menu">
                        <div class="col12 mobile-only">
                            <li class="menu-item spacer"><span><?php echo lang_snippet('menu');?></span></li>
                        </div>
                        <li class="menu-item"><a href="/movies" title="<?php echo lang_snippet('movies'); ?>"><?php echo lang_snippet('movies'); ?></a></li>
                        <li class="menu-item"><a href="/shows" title="<?php echo lang_snippet('shows'); ?>"><?php echo lang_snippet('shows'); ?></a></li>
                        <li class="menu-item"><a href="/my-list" title="<?php echo lang_snippet('list'); ?>"><?php echo lang_snippet('list'); ?></a></li>
                        <div class="col12 mobile-only marg-top-m">
                            <li class="menu-item spacer"><span><?php echo lang_snippet('admin').' '.lang_snippet('menu');?></span></li>

                            <?php echo adminMenu('main-menu');?>
                        </div>
                    </ul>
                </nav>

                <!-- Profil -->
                <button href="#" id="user-menu-btn">
                    <figure class="square">
                        <img data-img="<?php echo userProfileImg(); ?>" loading="lazy" alt="">
                    </figure>

                    <menu class="user-menu">
                        <ul>
                            <?php echo adminMenu('user-menu');?>
                            
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