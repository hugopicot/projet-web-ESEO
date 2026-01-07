<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Jeu de Mots Mêlés</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        body {
            font-family: 'Inter';
        }

        h1 {
            text-align: center;
            margin: 1rem 0;
        }

        .zone-jeu {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            margin-top: 20px;
        }

        .info-top {
            display: flex;
            justify-content: space-around;
            width: 100%;
            max-width: 600px;
            margin-bottom: 1rem;
            gap: 2rem;
        }

        .info-item {
            text-align: center;
            font-size: 16px;
        }

        .info-label {
            color: #666;
            font-size: 14px;
            margin-bottom: 4px;
        }

        .info-value {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .difficulte-info {
            text-align: center;
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 1rem;
            color: #333;
        }

        #mots-a-trouver {
            list-style: none;
            display: flex;
            width: 100%;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 0;
            justify-content: center;
        }

        #mots-a-trouver li {
            padding: 6px 12px;
            background: #ececec;
            border-radius: 6px;
            font-size: 18px;
        }

        #mots-a-trouver li.trouve {
            text-decoration: line-through;
            background: #b8f3b3;
        }

        #grille {
            display: grid;
            gap: 4px;
            background-color: blue;
            padding: 1rem;
        }

        .mots-trouver-div {
        }

        .mots-trouver-div ul {
            width: 100%;
        }

        .case {
            width: 40px;
            height: 40px;
            border: 1px solid #444;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            background: #fff;
            border-radius: 5px;
            transition: 0.2s;
        }

        .case:hover {
            background: #d7d7ff;
        }

        .case.selectionnee {
            background: #ffe38c;
        }

        .case.trouvee {
            background: #afffb0 !important;
        }

        .modal-content {
            border: none;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        }

        .modal-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px 10px 0 0;
        }

        .score-final {
            font-size: 48px;
            font-weight: bold;
            color: #667eea;
            text-align: center;
            margin: 20px 0;
        }

        .game-complete {
            text-align: center;
            padding: 20px 0;
        }

        .game-complete h5 {
            color: #333;
            margin-bottom: 15px;
        }

        #end-screen {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.85);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            text-align: center;
            font-family: 'Inter', sans-serif;
            padding: 2rem;
            box-sizing: border-box;
        }

        #end-screen h2 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            color: #43a047;
        }

        #end-screen p {
            font-size: 1.8rem;
            margin: 0.8rem 0;
        }

        .score-final {
            font-size: 4rem !important;
            font-weight: bold;
            color: #667eea;
            margin: 1.5rem 0 !important;
        }

        #end-screen button {
            padding: 1rem 2rem;
            font-size: 1.3rem;
            background-color: #43a047;
            color: white;
            border: none;
            border-radius: 12px;
            cursor: pointer;
            margin: 0.5rem;
            transition: background-color 0.3s;
        }

        #end-screen button:hover {
            background-color: #388e3c;
        }

        #end-screen button:nth-of-type(2) {
            background-color: #667eea;
        }

        #end-screen button:nth-of-type(2):hover {
            background-color: #764ba2;
        }
    </style>
</head>
<body>
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

    <div class="container">
        <h1>Mots Mêlés</h1>

        <div class="mb-3 text-center">
            <div class="difficulte-info" id="difficulte-text">
                Facile (10x10)
            </div>
        </div>

        <div class="info-top">
            <div class="info-item">
                <div class="info-label">Temps</div>
                <div class="info-value" id="timer">0s</div>
            </div>
            <div class="info-item">
                <div class="info-label">Score</div>
                <div class="info-value" id="score">0</div>
            </div>
        </div>

        <div class="zone-jeu">
            <div class="mots-trouver-div">
                <ul id="mots-a-trouver"></ul>
            </div>
            <div id="grille"></div>
        </div>
    </div>

    <div id="end-screen">
        <h2>Félicitations !</h2>
        <p id="end-message">Vous avez trouvé tous les mots !</p>
        <p>Temps écoulé : <strong id="end-time">0 secondes</strong></p>
        <p class="score-final" id="end-points">0 points</p>
        <div>
            <button onclick="recommencerJeu()">Jouer à nouveau</button>
            <button onclick="window.location.href='mots.php'">Retour aux Jeux</button>
        </div>
    </div>


    <script>
        let mots = [];

        // Fonction pour obtenir des mots aléatoires depuis la base de données
        async function obtenirMotsAleatoires() {
            try {
                const response = await fetch('get_random_words.php');
                const data = await response.json();

                if (data.success) {
                    mots = data.words;
                    return true;
                } else {
                    console.error('Erreur lors de la récupération des mots:', data.error);
                    // Mots par défaut en cas d'erreur
                    mots = ["CHAT", "SOLEIL", "MOMILI", "LUNE", "ETOILE", "ARBRE", "MAISON", "FLEUR", "MER", "NEIGE", "CHIEN", "OISEAU"];
                    return true;
                }
            } catch (error) {
                console.error('Erreur de réseau:', error);
                // Mots par défaut en cas d'erreur
                mots = ["CHAT", "SOLEIL", "MOMILI", "LUNE", "ETOILE", "ARBRE", "MAISON", "FLEUR", "MER", "NEIGE", "CHIEN", "OISEAU"];
                return true;
            }
        }

        let taille = 10;
        let grille = [];
        let placements = [];
        let motsTrouves = 0;
        let jeuTermine = false;

        // Système de points
        const pointsBase = {
            10: 400,
            15: 700,
            20: 1000
        };

        const tempsReference = {
            10: 180,
            15: 300,
            20: 420
        };

        function getCoeffcientTemps(tempsEnSecondes, talle) {
            const tempsRef = tempsReference[talle];
            const difference = tempsEnSecondes - tempsRef;

            if (tempsEnSecondes <= tempsRef) {
                return 1.00;
            } else if (difference <= 60) {
                return 0.85;
            } else if (difference <= 120) {
                return 0.70;
            } else if (difference <= 180) {
                return 0.55;
            } else {
                return 0.40;
            }
        }

        function calculerPointage(tempsEnSecondes, talle) {
            const pointsBaseValue = pointsBase[talle];
            const coefficent = getCoeffcientTemps(tempsEnSecondes, talle);
            const pointageFinal = Math.round(pointsBaseValue * coefficent);
            return pointageFinal;
        }

        // Fonction pour afficher les mots à trouver
        function afficherMots() {
            const liste = document.getElementById("mots-a-trouver");
            liste.innerHTML = ""; // Vider la liste existante
            mots.forEach(mot => {
                const li = document.createElement("li");
                li.textContent = mot;
                li.id = "mot-" + mot;
                liste.appendChild(li);
            });
        }

        let temps = 0;
        let timerId = null;
        function demarrerTimer() {
            timerId = setInterval(() => {
                temps++;
                const secondes = temps;
                document.getElementById("timer").textContent = secondes + "s";
            }, 1000);
        }
        const grilleDiv = document.getElementById("grille");

        const directions = [
            {dx: 1, dy: 0},
            {dx: -1, dy: 0},
            {dx: 0, dy: 1},
            {dx: 0, dy: -1},
            {dx: 1, dy: 1},
            {dx: -1, dy: -1},
            {dx: 1, dy: -1},
            {dx: -1, dy: 1}
        ];

        function peutPlacer(mot, x, y, dir) {
            for (let i = 0; i < mot.length; i++) {
                const nx = x + i * dir.dx;
                const ny = y + i * dir.dy;
                if (nx < 0 || nx >= taille || ny < 0 || ny >= taille) return false;
                if (grille[ny * taille + nx] !== "" && grille[ny * taille + nx] !== mot[i]) return false;
            }
            return true;
        }

        function placerMot(mot, x, y, dir) {
            const positions = [];
            for (let i = 0; i < mot.length; i++) {
                const nx = x + i * dir.dx;
                const ny = y + i * dir.dy;
                grille[ny * taille + nx] = mot[i];
                positions.push(ny * taille + nx);
            }
            placements.push({mot, positions});
        }

        function placerTousLesMots() {
            const motsMelanges = [...mots].sort(() => Math.random() - 0.5);

            for (const mot of motsMelanges) {
                let place = false;
                let tentatives = 0;
                while (!place && tentatives < 1000) {
                    const dir = directions[Math.floor(Math.random() * directions.length)];
                    const maxX = taille - (dir.dx !== 0 ? (mot.length - 1) * Math.abs(dir.dx) : 0);
                    const maxY = taille - (dir.dy !== 0 ? (mot.length - 1) * Math.abs(dir.dy) : 0);
                    const x = Math.floor(Math.random() * maxX);
                    const y = Math.floor(Math.random() * maxY);
                    if (peutPlacer(mot, x, y, dir)) {
                        placerMot(mot, x, y, dir);
                        place = true;
                    }
                    tentatives++;
                }
                if (!place) {
                    console.warn("Impossible de placer le mot : " + mot);
                }
            }
        }

        function genererGrille(nouvelleTaille) {
            taille = nouvelleTaille;
            grille = Array(taille * taille).fill("");
            placements = [];
            grilleDiv.innerHTML = "";
            grilleDiv.style.gridTemplateColumns = `repeat(${taille}, 40px)`;
            motsTrouves = 0;
            jeuTermine = false;

            // Actualiser le texte de difficulté
            const difficultText = {
                10: "Facile (10x10)",
                15: "Moyenne (15x15)",
                20: "Difficile (20x20)"
            };
            document.getElementById("difficulte-text").textContent = difficultText[nouvelleTaille] || "Facile (10x10)";

            placerTousLesMots();

            for (let i = 0; i < grille.length; i++) {
                if (grille[i] === "") {
                    grille[i] = String.fromCharCode(65 + Math.floor(Math.random() * 26));
                }
            }

            grille.forEach((lettre, index) => {
                const div = document.createElement("div");
                div.classList.add("case");
                div.textContent = lettre;
                div.dataset.index = index;

                div.addEventListener("click", () => {
                    div.classList.toggle("selectionnee");
                    verifierSelection();
                });

                grilleDiv.appendChild(div);
            });
        }

        function actualiserScore() {
            if (motsTrouves === mots.length) {
                const pointage = calculerPointage(temps, taille);
                document.getElementById("score").textContent = pointage;
                return pointage;
            } else {
                document.getElementById("score").textContent = "0";
                return 0;
            }
        }

        function verifierSelection() {
            const casesSelectionnees = [...document.querySelectorAll(".case.selectionnee")];
            if (casesSelectionnees.length === 0) return;

            const indices = casesSelectionnees.map(c => parseInt(c.dataset.index)).sort((a,b) => a - b);
            const selection = casesSelectionnees.map(c => c.textContent).join("");

            for (const {mot, positions} of placements) {
                const posTriees = [...positions].sort((a,b) => a - b);
                if (posTriees.length === indices.length && posTriees.every((v, i) => v === indices[i])) {
                    if (selection === mot || selection === mot.split('').reverse().join('')) {
                        casesSelectionnees.forEach(c => {
                            c.classList.remove("selectionnee");
                            c.classList.add("trouvee");
                        });
                        document.getElementById("mot-" + mot).classList.add("trouve");
                        motsTrouves++;
                        actualiserScore();

                        // Vérifier si le jeu est complété
                        if (motsTrouves === mots.length && !jeuTermine) {
                            jeuTermine = true;
                            terminerJeu();
                        }

                        return;
                    }
                }
            }
        }

        function terminerJeu() {
            clearInterval(timerId);
            const pointageFinal = calculerPointage(temps, taille);

            // Actualiser score en temps réel
            document.getElementById("score").textContent = pointageFinal;

            // Afficher la pantalla final
            document.getElementById("end-time").textContent = temps + " secondes";
            document.getElementById("end-points").textContent = pointageFinal + " points";

            document.getElementById("end-screen").style.display = "flex";

            // Enregistrer le score dans la base de données
            enregistrerPointage(pointageFinal);
        }

        function enregistrerPointage(pointage) {
            // Obtenir id_grille s'il existe dans l'URL
            const urlParams = new URLSearchParams(window.location.search);
            const id_grille = urlParams.get('id');

            const formData = new FormData();
            formData.append('pointage', pointage);
            formData.append('taille', taille);
            formData.append('temps', temps);
            formData.append('id_grille', id_grille || '');

            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Pointage enregistré:', data);
            })
            .catch(error => {
                console.error('Erreur lors de l\'enregistrement du pointage:', error);
            });
        }

        // Initialiser le jeu avec des mots aléatoires
        async function initialiserJeu() {
            await obtenirMotsAleatoires();
            afficherMots();
            genererGrille(10);
            demarrerTimer();
        }

        // Fonction pour recommencer le jeu avec de nouveaux mots
        async function recommencerJeu() {
            // Réinitialiser les variables du jeu
            motsTrouves = 0;
            jeuTermine = false;
            temps = 0;
            document.getElementById("timer").textContent = "0s";
            document.getElementById("score").textContent = "0";

            // Cacher l'écran de fin
            document.getElementById("end-screen").style.display = "none";

            // Obtenir de nouveaux mots et réinitialiser le jeu
            await obtenirMotsAleatoires();
            afficherMots();
            genererGrille(10);
            demarrerTimer();
        }

        initialiserJeu();
    </script>

    <?php
    // Traiter la soumission du score si c'est une requête POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pointage'])) {
        header('Content-Type: application/json');

        try {
            include '../includes/db.php';

            $pointage = (int)$_POST['pointage'];
            $taille = (int)$_POST['taille'];
            $temps = (int)$_POST['temps'];
            $id_grille = !empty($_POST['id_grille']) ? (int)$_POST['id_grille'] : null;

            // Pour les utilisateurs non connectés, utiliser un ID utilisateur par défaut (anonyme)
            // ou créer une session anonyme
            if (!isset($_SESSION['id_utilisateur'])) {
                // Vérifier si on a déjà un utilisateur anonyme pour cette session
                if (!isset($_SESSION['id_utilisateur_anon'])) {
                    // Créer un utilisateur anonyme temporaire (id_utilisateur = 1 pour les tests)
                    $_SESSION['id_utilisateur_anon'] = 1; // Utilisateur par défaut pour les parties anonymes
                }
                $id_utilisateur = $_SESSION['id_utilisateur_anon'];
            } else {
                $id_utilisateur = $_SESSION['id_utilisateur'];
            }

            // Créer une nouvelle partie
            $stmt = $pdo->prepare("
                INSERT INTO partie (date_debut, date_fin, score, statut, id_utilisateur, id_grille)
                VALUES (NOW(), NOW(), :pointage, 'completed', :id_utilisateur, :id_grille)
            ");

            $stmt->execute([
                ':pointage' => $pointage,
                ':id_utilisateur' => $id_utilisateur,
                ':id_grille' => $id_grille
            ]);

            $id_partie = $pdo->lastInsertId();

            // Enregistrer dans le leaderboard
            $stmt_leaderboard = $pdo->prepare("
                INSERT INTO leaderboard (score, date_entree, id_partie)
                VALUES (:pointage, NOW(), :id_partie)
            ");

            $stmt_leaderboard->execute([
                ':pointage' => $pointage,
                ':id_partie' => $id_partie
            ]);

            echo json_encode([
                'success' => true,
                'id_partie' => $id_partie,
                'pointage' => $pointage,
                'message' => 'Pointage enregistré avec succès'
            ]);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Erreur dans la base de données: ' . $e->getMessage()
            ]);
        }

        exit; // Terminer l'exécution pour les requêtes AJAX
    }
    ?>
</body>
</html>