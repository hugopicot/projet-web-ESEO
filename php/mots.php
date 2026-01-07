<?php
session_start();

// =========================================
// CONNEXION A LA BASE
// =========================================
require_once __DIR__ . '/../includes/db.php';
$conn = $pdo;

    // Récupérer toutes les grilles du jeu
    $stmt = $conn->prepare("SELECT * FROM grille ORDER BY date_creation DESC");
    $stmt->execute();
    $grilles = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Jeux</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <style>
            body {
                /* background: #f5f8ff; */
            }

            /* header {
                background: white;
                padding: 15px 0;
                box-shadow: 0 1px 6px rgba(0,0,0,0.1);
            } */

            .nav-link-custom {
                font-size: 18px;
                margin: 0 20px;
                font-weight: 500;
                cursor: pointer;
            }
            .nav-link-custom:hover {
                color: #0d6efd;
            }

            .page-title {
                text-align: center;
                margin-top: 30px;
                font-size: 40px;
                color: #333;
                font-weight: bold;
            }

            .container-jeux {
                margin-top: 40px;
                display: flex;
                flex-direction: column;
                gap: 25px;
                align-items: center;
            }

            .card-jeu {
                width: 80%;
                max-width: 850px;
                padding: 25px;
                background: white;
                border-radius: 15px;
                border: 1px solid #dbe2ff;
                display: flex;
                align-items: center;
                justify-content: space-between;
            }

            .jeu-info h3 {
                margin: 0;
                font-size: 22px;
                font-weight: bold;
            }

            .jeu-info p {
                margin: 5px 0 0;
                color: #666;
            }

            .btn-jeu {
                padding: 12px 24px;
                background: #0d6efd;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 17px;
                transition: 0.2s;
            }
            .btn-jeu:hover {
                background: #0a58ca;
            }
        </style>
    </head>
    <body>
    <!-- ============================
        HEADER
    =============================== -->
    <header>
        <!-- ================= NAVBAR ================= -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom position-relative">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="../index.php">
                    <img src="../assets/logo.png" alt="Logo" height="40" class="me-2">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav navbar-nav-centered">
                        <li class="nav-item"><a class="nav-link fw-semibold" href="mots.php">Jeux</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold" href="leaderboard.php">Leaderboard</a></li>
                    </ul>

                    <div class="d-flex ms-auto nav-auth">
                    <?php if (isset($_SESSION["id"])): ?>
                        <a class="btn btn-outline-dark me-2" href="admin.php">Admin</a>
                        <a class="btn btn-outline-dark me-2" href="profil.php">Mon Profil</a>
                        <a class="btn btn-dark" href="deconexion.php">Déconnexion</a>
                    <?php else: ?>
                        <a class="btn btn-outline-dark me-2" href="account.php?mode=login">Connexion</a>
                        <a class="btn btn-dark" href="account.php?mode=register">S'inscrire</a>
                    <?php endif; ?>
                </div>
                </div>
            </div>
        </nav>
    </header>


    <h1 class="page-title mt-4">Liste des Jeux</h1>
        <div class="container-jeux">
            <!-- ----------- Mot du jour ----------- -->
            <div class="card-jeu">
                <div class="jeu-info">
                    <h3>Mot du jour</h3>
                    <p>Découvrez un nouveau mot chaque jour !</p>
                </div>
                <a href="jeu-mot-mystere.php"><button class="btn-jeu">Jouer</button></a>
            </div>

            <!-- ----------- Mots mêlés  ----------- -->
            <?php foreach($grilles as $g) :?>
            <div class="card-jeu">
                    <div class="jeu-info">
                        <h3>Mot mélee</h3>
                        <p>Niveau <?= htmlspecialchars($g['difficulte']) ?> !</p>
                    </div>
                    <a href="jeu-mot-melee.php?id=<?= $g['id_grille'] ?>">
                        <button class="btn-jeu">Jouer</button>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>

    </body>
</html>
