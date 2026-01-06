<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>JeuxNova – Jeux de mots quotidiens</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>

<body class="bg-white text-dark">

<!-- ================= NAVBAR ================= -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom position-relative">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
            <img src="logo.png" height="40" class="me-2" alt="JeuxNova">
            <span class="fw-bold">JeuxNova</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="menu">
            <ul class="navbar-nav navbar-nav-centered">
                <!-- <li class="nav-item"><a class="nav-link" href="/projet-web-eseo/php/motdujour.php">Mot du jour</a></li> -->
                <li class="nav-item"><a class="nav-link" href="/projet-web-eseo/php/jeu-mot-mystere.php">Mot du jour</a></li>
                <li class="nav-item"><a class="nav-link" href="/projet-web-eseo/php/jeu-mot-melee.php">Mots fléchés</a></li>
                <li class="nav-item"><a class="nav-link" href="/projet-web-eseo/php/leaderboard.php">Leaderboard</a></li>
            </ul>

            <div class="d-flex ms-auto nav-auth">
                <?php if (isset($_SESSION["id"])): ?>
                    <span class="me-3 fw-semibold">
                        Bonjour, <?= htmlspecialchars($_SESSION["pseudo"]) ?>
                    </span>
                    <a href="./php/profil.php" class="btn btn-outline-dark me-2">Profil</a>
                    <a href="./php/admin.php" class="btn btn-outline-dark me-2">Admin</a>
                    <a href="./php/deconexion.php" class="btn btn-outline-dark">Déconnexion</a>
                <?php else: ?>
                    <a href="./php/admin.php" class="btn btn-outline-dark me-2">Admin</a>
                    <a href="./php/account.php?mode=login" class="btn btn-outline-dark me-2">Connexion</a>
                    <a href="./php/account.php?mode=register" class="btn btn-dark">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- ================= HERO ================= -->
<header class="bg-light border-bottom py-5">
    <div class="container">
        <div class="card shadow-sm border-0">
            <div class="card-body text-center py-5">
                <h1 class="display-6 fw-bold mb-3">
                    Des jeux de mots intelligents,<br class="d-none d-md-block">
                    chaque jour.
                </h1>
                <p class="lead mb-4">
                    Une plateforme moderne pour jouer, progresser et comparer vos performances
                    sur des jeux de lettres quotidiens.
                </p>

                <?php if (!isset($_SESSION["id"])): ?>
                    <a href="/projet-web-eseo/php/account.php?mode=register" class="btn btn-dark btn-lg me-2">Créer un compte</a>
                    <a href="/projet-web-eseo/php/account.php?mode=login" class="btn btn-outline-dark btn-lg">Se connecter</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<!-- ================= CONTENU ================= -->
<main class="container my-5">

    <!-- CONCEPT -->
    <section class="mb-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="fw-bold mb-3">Le concept JeuxNova</h2>
                <p>
                    JeuxNova est une plateforme dédiée aux jeux de réflexion autour des mots.
                    Chaque jour, de nouveaux défis sont proposés pour stimuler votre logique,
                    enrichir votre vocabulaire et mesurer vos performances.
                </p>

                <div class="row mt-4">
                    <div class="col-md-3 fw-semibold">🧠 Réflexion</div>
                    <div class="col-md-3 fw-semibold">📅 Défis quotidiens</div>
                    <div class="col-md-3 fw-semibold">🏆 Classements</div>
                    <div class="col-md-3 fw-semibold">🔒 Sécurité</div>
                </div>
            </div>
        </div>
    </section>

    <!-- FONCTIONNALITÉS -->
    <section class="mb-5">
        <h2 class="fw-bold mb-4">Fonctionnalités</h2>

        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold">Mot du jour</h5>
                        <p>
                            Un mot identique pour tous les joueurs chaque jour,
                            avec un système de score basé sur votre performance.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold">Mots fléchés</h5>
                        <p>
                            Des grilles de différents niveaux de difficulté,
                            conçues pour s’adapter à tous les profils de joueurs.
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-semibold">Leaderboard</h5>
                        <p>
                            Comparez vos résultats avec les autres joueurs
                            grâce à un classement mis à jour automatiquement.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- RGPD -->
    <section class="mb-5">
        <div class="card shadow-sm border">
            <div class="card-body">
                <h2 class="fw-bold mb-3">Protection des données & RGPD</h2>

                <p>
                    JeuxNova respecte la réglementation européenne relative
                    à la protection des données personnelles (RGPD).
                </p>

                <ul>
                    <li>Données collectées : email, pseudo</li>
                    <li>Mots de passe stockés de manière sécurisée (hachage)</li>
                    <li>Aucune revente ou partage des données</li>
                    <li>Droit d’accès, de modification et de suppression du compte</li>
                </ul>

                <p class="mb-0">
                    Contact RGPD : <strong>support@jeuxnova.com</strong>
                </p>
            </div>
        </div>
    </section>

</main>

<!-- ================= FOOTER ================= -->
<footer class="bg-light border-top py-4 text-center text-secondary">
    <div class="container">
        <p class="mb-1">© 2025 JeuxNova — Tous droits réservés</p>
        <small>
            Plateforme de jeux de réflexion · Conformité RGPD · support@jeuxnova.com
        </small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
