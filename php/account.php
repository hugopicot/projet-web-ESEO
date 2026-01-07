<?php
require "config.php";

$erreur_login = "";
$erreur_register = "";
$mode = isset($_GET["mode"]) ? $_GET["mode"] : "login"; // login par défaut

/* ---------------------------------------
   TRAITEMENT CONNEXION
--------------------------------------- */
if (isset($_POST["login"])) {

    $email = trim($_POST["email"]);
    $mdp = $_POST["mot_de_passe"];

    $req = $db->prepare("SELECT * FROM utilisateur WHERE email = ?");
    $req->execute([$email]);
    $user = $req->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($mdp, $user["mot_de_passe"])) {
        $_SESSION["id"] = $user["id_utilisateur"];
        $_SESSION["pseudo"] = $user["pseudo"];
        $_SESSION["role"] = $user["role"];

        header("Location: ../index.php");
        exit;
    } else {
        $erreur_login = "Identifiants incorrects.";
        $mode = "login";
    }
}

/* ---------------------------------------
   TRAITEMENT INSCRIPTION
--------------------------------------- */
if (isset($_POST["register"])) {

    $email = trim($_POST["email"]);
    $pseudo = trim($_POST["pseudo"]);
    $mdp = $_POST["mot_de_passe"];
    $mdp2 = $_POST["mot_de_passe2"];

    if ($mdp !== $mdp2) {
        $erreur_register = "Les mots de passe ne correspondent pas.";
        $mode = "register";
    } else {
        $req = $db->prepare("SELECT id_utilisateur FROM utilisateur WHERE email = ?");
        $req->execute([$email]);

        if ($req->rowCount() > 0) {
            $erreur_register = "Un compte existe déjà avec cet email.";
            $mode = "register";
        } else {
            $hash = password_hash($mdp, PASSWORD_DEFAULT);

            $insert = $db->prepare("
                INSERT INTO utilisateur (email, mot_de_passe, pseudo, role, date_creation)
                VALUES (?, ?, ?, 'user', NOW())
            ");
            $insert->execute([$email, $hash, $pseudo]);

            header("Location: account.php?mode=login&success=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="bg-white text-dark">

<!-- NAVBAR -->
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

<!-- PAGE -->
<div class="container mt-5 col-md-5">

    <?php if (isset($_GET["success"])): ?>
        <div class="alert alert-success">Compte créé avec succès ! Vous pouvez vous connecter.</div>
    <?php endif; ?>

    <!-- FORMULAIRE CONNEXION -->
    <?php if ($mode === "login"): ?>
        <h2 class="mb-4">Connexion</h2>

        <?php if (!empty($erreur_login)) echo "<div class='alert alert-danger'>$erreur_login</div>"; ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" class="form-control mb-3" required>

            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" class="form-control mb-3" required>

            <button class="btn btn-dark w-100" name="login">Connexion</button>
        </form>

        <p class="mt-3 text-center">
            Pas de compte ?
            <a href="account.php?mode=register">Créer un compte</a>
        </p>

        <!-- FORMULAIRE INSCRIPTION -->
    <?php else: ?>
        <h2 class="mb-4">Créer un compte</h2>

        <?php if (!empty($erreur_register)) echo "<div class='alert alert-danger'>$erreur_register</div>"; ?>

        <form method="POST">
            <label>Email</label>
            <input type="email" name="email" class="form-control mb-3" required>

            <label>Pseudo</label>
            <input type="text" name="pseudo" class="form-control mb-3" required>

            <label>Mot de passe</label>
            <input type="password" name="mot_de_passe" class="form-control mb-3" required>

            <label>Confirmer le mot de passe</label>
            <input type="password" name="mot_de_passe2" class="form-control mb-3" required>

            <button class="btn btn-dark w-100" name="register">S'inscrire</button>
        </form>

        <p class="mt-3 text-center">
            Déjà un compte ?
            <a href="account.php?mode=login">Se connecter</a>
        </p>
    <?php endif; ?>
</div>

<!-- FOOTER -->
<footer class="mt-5 py-4 bg-light border-top text-center text-secondary">
    <div class="container">
        <p class="mb-1">© 2025 JeuxNova — Tous droits réservés</p>
        <small>Contact : support@jeuxnova.com</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
