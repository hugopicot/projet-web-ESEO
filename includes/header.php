<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$docRoot = rtrim(str_replace('\\','/', $_SERVER['DOCUMENT_ROOT']), '/');
$projectRoot = rtrim(str_replace('\\','/', realpath(__DIR__ . '/..')), '/');
$basePath = str_replace($docRoot, '', $projectRoot);
$root = ($basePath === '') ? '/' : $basePath . '/';
?>
<header>
    <nav class="navbar">
        <div class="logo">
            <a href="<?php echo $root; ?>index.php">
                <img src="<?php echo $root; ?>assets/logo.png" alt="Logo" style="height: 40px;">
            </a>
        </div>
        <ul class="nav-links">
            <li><a href="<?php echo $root; ?>index.php">Accueil</a></li>
            <li><a href="<?php echo $root; ?>pages/games.php">Jeux</a></li>
            <li><a href="<?php echo $root; ?>pages/leaderboard.php">Leaderboard</a></li>
        </ul>
        <div class="user-menu">
            <a href="<?php echo $root; ?>pages/profil.php" class="profile-link">
                <div class="avatar-small">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <circle cx="12" cy="12" r="11" stroke="#8A8AFF" stroke-width="1.5"/>
                        <circle cx="12" cy="9" r="3.5" stroke="#8A8AFF" stroke-width="1.5"/>
                        <path d="M5.5 18.5C6.5 16 8.5 14.5 12 14.5C15.5 14.5 17.5 16 18.5 18.5" stroke="#8A8AFF" stroke-width="1.5" stroke-linecap="round"/>
                    </svg>
                </div>
            </a>
        </div>
    </nav>
    <style>
        header {
            background-color: #fcfcfc;
            padding: 15px 40px;
            display: flex;
            justify-content: center;
        }
        .navbar {
            width: 100%;
            max-width: 1200px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav-links {
            list-style: none;
            display: flex;
            gap: 30px;
            margin: 0;
            padding: 0;
        }
        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            font-size: 16px;
        }
        .nav-links a:hover {
            color: #666;
        }
        .logo a {
            text-decoration: none;
            color: black;
        }
        .avatar-small svg {
            display: block;
        }
    </style>
</header>
