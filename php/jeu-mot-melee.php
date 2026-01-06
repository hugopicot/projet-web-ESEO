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
            /* margin-top: 0rem; */
        }

        .container {
            /* padding: 2rem 4rem; */
            /* border-radius: 10px; */
            /* box-shadow: 0 0 15px rgba(0,0,0,0.15); */
            /* max-width:1000px;
            width: 100%;
            height:100%; */
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

        #mots-a-trouver {
            list-style: none;
            display: flex;
            width: 100%;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 1rem;
            padding: 0;
            /* padding: 0;
            width: 100%;
            max-width: 100%; */
        }

        #mots-a-trouver li {
            padding: 6px 12px;
            background: #ececec;
            border-radius: 6px;
            /* margin-bottom: 8px; */
            font-size: 18px;
            /* font-weight: bold; */
            /* text-align: center; */
            /* transition: 0.2s; */
            /* width: 150px; */
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
            /* background-color: red; */
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
                    <li class="nav-item"><a class="nav-link fw-semibold" href="/projet-web-eseo/php/mots.php">Jeux</a></li>
                    <li class="nav-item"><a class="nav-link fw-semibold" href="/projet-web-eseo/php/leaderboard.php">Leaderboard</a></li>
                </ul>

                <div class="d-flex ms-auto nav-auth">
                <?php if (isset($_SESSION["id"])): ?>
                    <a class="btn btn-outline-dark me-2" href="/projet-web-eseo/php/profil.php">Mon Profil</a>
                    <a class="btn btn-dark" href="/projet-web-eseo/php/deconexion.php">Déconnexion</a>
                <?php else: ?>
                    <a class="btn btn-outline-dark me-2" href="/projet-web-eseo/php/account.php?mode=login">Connexion</a>
                    <a class="btn btn-dark" href="/projet-web-eseo/php/account.php?mode=register">S'inscrire</a>
                <?php endif; ?>
            </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1>Mots Mêlés</h1>

        <div class="mb-3 text-center">
            <select id="difficulte" class="form-select w-50 mx-auto">
                <option value="10">Facile (10x10)</option>
                <option value="15">Moyenne (15x15)</option>
                <option value="20">Difficile (20x20)</option>
            </select>
        </div>

        <div class="zone-jeu">
            <div>
                Temps écoulé : <span id="timer">0</span> secondes
            </div>
            <div class="mots-trouver-div">
                <ul id="mots-a-trouver"></ul>
            </div>
            <div id="grille"></div>
        </div>
    </div>

    <script>
        const mots = ["CHAT", "SOLEIL", "MOMILI", "LUNE", "ETOILE", "ARBRE", "MAISON", "FLEUR", "MER", "NEIGE","CHIEN","OISEAU"];

        let taille = 10;
        let grille = [];
        let placements = [];

        const liste = document.getElementById("mots-a-trouver");
        mots.forEach(mot => {
            const li = document.createElement("li");
            li.textContent = mot;
            li.id = "mot-" + mot;
            liste.appendChild(li);
        });

        let temps = 0;
        let timerId=null;
        function demarrerTimer() {
            timerId = setInterval(() => {
                temps++;
                document.getElementById("timer").textContent = temps;
            }, 1000);
        }
        demarrerTimer();

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
                        alert("Bravo ! Tu as trouvé le mot : " + mot);
                        return;
                    }
                }
            }
        }

        document.getElementById("difficulte").addEventListener("change", (e) => {
            genererGrille(parseInt(e.target.value));
        });

        genererGrille(10);
    </script>
</body>
</html>