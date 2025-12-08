<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Jeux Quotidiens</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="styles.css" />
</head>
<body class="bg-white text-dark">

<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom position-relative">
    <div class="container">

        <!-- Logo à gauche (toujours visible) -->
        <a class="navbar-brand d-flex align-items-center" href="#">
            <img src="logo.png" alt="Logo" height="40" class="me-2" />
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <!-- Liens centrés (positionnés absolument par le CSS ci-dessous) -->
            <ul class="navbar-nav navbar-nav-centered">
                <li class="nav-item"><a class="nav-link fw-semibold" href="#motdujour">Mot du Jour</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#motsfleches">Mots Fléchés</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="#leaderboard">Leaderboard</a></li>
            </ul>

            <!-- Boutons à droite (flottent naturellement à droite) -->
            <div class="d-flex ms-auto nav-auth">
                <a class="btn btn-outline-dark me-2" href="php/account.php">Connexion</a>
                <a class="btn btn-dark" href="php/account.php">S'inscrire</a>
            </div>
        </div>
    </div>
</nav>



<header class="py-5 text-center bg-light border-bottom">
    <div class="container">
        <h1 class="display-5 fw-bold">Jouez Chaque Jour</h1>
        <p class="lead">Connectez-vous, jouez, progressez et grimpez dans les classements !</p>
        <a href="php/account.php" class="btn btn-dark btn-lg mt-3">Créer un compte</a>
    </div>
</header>


<main class="container mt-5">
    <div class="row g-4">
        <div class="col-md-6">
            <div class="card border shadow-sm p-3 h-100">
                <h3 class="fw-bold">Mot du Jour</h3>
                <p>Chaque jour un mot à deviner. Simple, addictif et amusant.</p>
                <a class="btn btn-outline-dark" href="#motdujour">Jouer</a>
            </div>
        </div>


        <div class="col-md-6">
            <div class="card border shadow-sm p-3 h-100">
                <h3 class="fw-bold">Mots Fléchés</h3>
                <p>Des grilles inédites mises à jour régulièrement.</p>
                <a class="btn btn-outline-dark" href="#motsfleches">Jouer</a>
            </div>
        </div>
    </div>
</main>


<footer class="mt-5 py-4 bg-light border-top text-center text-secondary">
    <div class="container">
        <p class="mb-1">© 2025 Jeux Quotidiens — Tous droits réservés</p>
        <small>Développé avec passion — Contact : support@jeuxnova.com</small>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>