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
                        <?php
                            $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
                            $sql = "SELECT role FROM users WHERE session='".$_COOKIE['session_id']."'";
                            $result = $conn->query($sql);
                            $role = $result->fetch_assoc();
                            if ($role['role'] > 0) {
                                echo '<li class="menu-item"><a href="/settings" title="'.lang_snippet('Settings').'">'.lang_snippet('Settings').'</a></li>';
                            }
                        ?>
                        
                        <li class="menu-item mobile-only"><a href="/logout" title="<?php echo lang_snippet('Logout'); ?>"><?php echo lang_snippet('Logout'); ?></a></li>
                    </ul>
                </nav>
                
                <?php 
                    $sql = 'SELECT id, user_img FROM users WHERE session="'.$_COOKIE['session_id'].'"';
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                     
                        // output data of each row
                        while($row = $result->fetch_assoc()) {
                            
                            if ($row['user_img'] === NULL || $row['user_img'] === '') {
                                $userProfileImg = '/build/css/images/placeholder.webp';
                            } else {
                                $userProfileImg = '/uploads/'.$row['user_img'];
                            }

                            $currentUserID = $row['id'];
                        }
                    } else {
                        header('Location: /logout');
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