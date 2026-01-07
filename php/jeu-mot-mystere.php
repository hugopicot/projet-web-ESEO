<?php
session_start();

// =========================================
// CONNEXION A LA BASE
// =========================================
require_once __DIR__ . '/../includes/db.php';
$conn = $pdo; 

    // Obtener el mot du jour y su id
    $stmt = $conn->prepare("SELECT id_mot, mot FROM motdujour ORDER BY RAND() LIMIT 1");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $motMystere = strtoupper($result['mot']);
    $currentMotId = $result['id_mot'];

    $userId = isset($_SESSION['id']) ? $_SESSION['id'] : 1; 

    $insertPartie = $conn->prepare("
        INSERT INTO partie (date_debut, statut, id_utilisateur, id_mot)
        VALUES (NOW(), 'en_cours', :user_id, :id_mot)
    ");
    $insertPartie->bindParam(':user_id', $userId);
    $insertPartie->bindParam(':id_mot', $currentMotId);
    $insertPartie->execute();
    $currentPartieId = $conn->lastInsertId();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="/js/mot-mystere.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Mot Mystere</title>
    <style>
        body {
            font-family: 'Inter';
            margin-top: 0rem;
        }
        /* JEU */
        .game-grid {
            display: grid;
            grid-template-columns: 1;
            grid-template-rows: repeat(5, 1fr);
        }
        
        .grid-container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .game-row {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 1rem;
            padding: 0.5rem;
        }

        .game-letter {
            width: 4rem;
            height: 4rem;
            border: 2px solid rgba(128, 128, 128, 0.356);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
        }


        /* KEYBOARD */

        .keyboard-container {
            width: 100%;
            margin-top: 10%;
            gap: 1rem;
            display: flex;
            flex-direction: column;
        }

        .keyboard-row {
            width: 100%;
            height: 30%;
            display: flex;
            align-items: center;
            justify-content: center;    
            gap: 1rem;
        }

        .keyboard-row div {
            border: 1px solid rgba(128, 128, 128, 0.356);
            border-radius: 10px;
            width: 100%;
            height: 4rem;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.25rem;
            font-weight: bold;
            background-color: #e2e8f0;
        }

        .keyboard-row div:hover {
            background-color: #a19e9e;
            cursor: pointer;
            transition: ease-in 0.2s;
        }


        .keyboard {
            width: 50%;
        }


        .page-container {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .backspace {
            max-width: 100%;
            max-height: 100%;
        }

        .check {
            max-width: 50%;
            max-height: 50%;
        }

        h1 {
            font-weight: bold;
            font-size: xx-large;
        }

        
        #end-screen {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            color: white;
            text-align: center;
            font-size: 1.8rem;
        }

        #end-screen h2 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        #end-screen p {
            font-size: 1.5rem;
            margin: 0.5rem 0;
        }

        #end-screen button {
            margin-top: 2rem;
            padding: 1rem 2rem;
            font-size: 1.2rem;
            background-color: #43a047;
            color: white;
            border: none;
            border-radius: 10px;
            cursor: pointer;
        }

        #end-screen button:hover {
            background-color: #388e3c;
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
    <div class="page-container">
        <!-- <p style="color: red; font-weight: bold;"><?php echo $motMystere; ?></p> -->
        <h1 class="page-title mt-4">Mot du Jour</h1>
        <div class="grid-container">
            <div class="game-grid">
                <div class="game-row">
                    <div class="game-letter" data-row="0" data-col="0" id="game-letter-container-1">
                    </div>
                    <div class="game-letter"  data-row="0" data-col="1" id="game-letter-container-2">
                    </div>
                    <div class="game-letter"  data-row="0" data-col="2" id="game-letter-container-3">
                    </div>
                    <div class="game-letter"  data-row="0" data-col="3" id="game-letter-container-4">
                    </div>
                    <div class="game-letter"  data-row="0" data-col="4" id="game-letter-container-5">
                    </div>
                </div>
                <div class="game-row">
                    <div class="game-letter" data-row="1" data-col="0" id="game-letter-container-6">
                    </div>
                    <div class="game-letter" data-row="1" data-col="1" id="game-letter-container-7">
                    </div>
                    <div class="game-letter" data-row="1" data-col="2" id="game-letter-container-8">
                    </div>
                    <div class="game-letter" data-row="1" data-col="3" id="game-letter-container-9">
                    </div>
                    <div class="game-letter" data-row="1" data-col="4" id="game-letter-container-10">
                    </div>
                </div>
                <div class="game-row">
                    <div class="game-letter" data-row="2" data-col="0" id="game-letter-container-11">
                    </div>
                    <div class="game-letter" data-row="2" data-col="1" id="game-letter-container-12">
                    </div>
                    <div class="game-letter" data-row="2" data-col="2" id="game-letter-container-13">
                    </div>
                    <div class="game-letter" data-row="2" data-col="3" id="game-letter-container-14">
                    </div>
                    <div class="game-letter" data-row="2" data-col="4" id="game-letter-container-15">
                    </div>
                </div>
                <div class="game-row">
                    <div class="game-letter" data-row="3" data-col="0" id="game-letter-container-16">
                    </div>
                    <div class="game-letter" data-row="3" data-col="1" id="game-letter-container-17">
                    </div>
                    <div class="game-letter" data-row="3" data-col="2" id="game-letter-container-18">
                    </div>
                    <div class="game-letter" data-row="3" data-col="3" id="game-letter-container-19">
                    </div>
                    <div class="game-letter" data-row="3" data-col="4" id="game-letter-container-20">
                    </div>
                </div>
                <div class="game-row">
                    <div class="game-letter" data-row="4" data-col="0" id="game-letter-container-21">
                    </div>
                    <div class="game-letter" data-row="4" data-col="1" id="game-letter-container-22">
                    </div>
                    <div class="game-letter" data-row="4" data-col="2" id="game-letter-container-23">
                    </div>
                    <div class="game-letter" data-row="4" data-col="3" id="game-letter-container-24">
                    </div>
                    <div class="game-letter" data-row="4" data-col="4" id="game-letter-container-25">
                    </div>
                </div>
            </div>
        </div>

        <div class="keyboard">
            <div class="keyboard-container">
                <div class="keyboard-row">
                    <div id="keyboard-letter-1">Q</div>
                    <div id="keyboard-letter-2">W</div>
                    <div id="keyboard-letter-3">E</div>
                    <div id="keyboard-letter-4">R</div>
                    <div id="keyboard-letter-5">T</div>
                    <div id="keyboard-letter-6">Y</div>
                    <div id="keyboard-letter-7">U</div>
                    <div id="keyboard-letter-8">I</div>
                    <div id="keyboard-letter-9">O</div>
                    <div id="keyboard-letter-10">P</div>
                </div>
    
                <div class="keyboard-row">
                    <div id="keyboard-letter-11">A</div>
                    <div id="keyboard-letter-12">S</div>
                    <div id="keyboard-letter-13">D</div>
                    <div id="keyboard-letter-14">F</div>
                    <div id="keyboard-letter-15">G</div>
                    <div id="keyboard-letter-16">H</div>
                    <div id="keyboard-letter-17">J</div>
                    <div id="keyboard-letter-18">K</div>
                    <div id="keyboard-letter-19">L</div>
                </div>
    
                <div class="keyboard-row">
                    <div><img src="../assets/check.svg" alt="check image" class="check" ></div>
                    <div id="keyboard-letter-20">Z</div>
                    <div id="keyboard-letter-21">X</div>
                    <div id="keyboard-letter-22">C</div>
                    <div id="keyboard-letter-23">V</div>
                    <div id="keyboard-letter-24">B</div>
                    <div id="keyboard-letter-25">N</div>
                    <div id="keyboard-letter-26">M</div>
                    <div><img src="../assets/backspace.svg" alt="backspace" class="backspace"></div>
                </div>
            </div>

        </div>
    </div>

    
    <div id="end-screen">
        <h2 id="end-title"></h2>
        <p id="end-message"></p>
        <p id="end-points"></p>
        <p id="end-word" style="font-size: 2rem; margin-top: 1rem;"></p>
        <button onclick="location.reload()">Jouer à nouveau</button>
        <a href="mots.php"><button>Retour à l'accueil</button></a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
<script>
    let magicWord = '<?php echo $motMystere; ?>'
    let currentRow = 0 //0 a 4
    let currentCol = 0 //0 a 4
    let gameOver = false //boolean
    let attemptsUsed = 0
    const partieId = <?php echo $currentPartieId; ?>

    const possibleLetters = ['q', 'w', 'e', 'r', 't', 'y', 'u', 'i', 'o', 'p', 'a', 's', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'z', 'x', 'c', 'v', 'b', 'n', 'm']
    for (const possibleLetter of possibleLetters) {
        document.addEventListener('keydown', function (event) {
            if (event.key === possibleLetter || event.key === possibleLetter.toLocaleUpperCase()) {
                const letter = event.key.toLocaleUpperCase()
                addLetter(letter)
            }
        })
    }
    
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Enter') {
            handleEnter()
        }
    })

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Backspace') {
            removeLetter()
        }
    })

    document.querySelector('.keyboard-container').addEventListener('click', function(e) {
        let key = e.target.closest('div');

        if (!key) return;

        const letter = key.textContent.trim().toLowerCase();

        if (key.querySelector('.check')) {
            handleEnter();
            return;
        }

        if (key.querySelector('.backspace')) {
            removeLetter();
            return;
        }

        if (possibleLetters.includes(letter)) {
            const clickedLetter = letter.toUpperCase();
            addLetter(clickedLetter);
        }
    });

    async function handleEnter() {
        // simple validation
        if (gameOver) return

        if (currentCol !== 5) {
            alert('Please enter all 5 letters')
            return
        }

        const guess = []
        for (let col = 0; col < 5; col++) {
            let cellsQuery = `.game-letter[data-row='${currentRow}'][data-col='${col}']`
            const cell = document.querySelector(cellsQuery)
            guess.push(cell.innerHTML)
        }
        const guessWord = guess.join('').toLowerCase()

        const isValid = await validateWord(guessWord)
        if (!isValid) {
            alert('Ce mot n\'existe pas')
            return
        }

        attemptsUsed = currentRow + 1
        checkRow()
    }

    async function validateWord(word) {
        return true
    }

    function getPoints(attempts) {
        if (attempts === 1) return 500
        if (attempts === 2) return 300
        if (attempts === 3) return 200
        if (attempts === 4) return 100
        if (attempts === 5) return 50
        if (attempts === 6) return 25
        return 0
    }

    async function saveGameResult(points, status) {
        await fetch('save-game.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                partie_id: partieId,
                score: points,
                statut: status
            })
        });
    }

    function showEndScreen(title, message, points, word = '') {
        document.getElementById('end-title').textContent = title;
        document.getElementById('end-message').textContent = message;
        document.getElementById('end-points').textContent = `Points obtenus : ${points}`;
        document.getElementById('end-word').textContent = word;
        document.getElementById('end-screen').style.display = 'flex';
    }

    function checkRow() {
        // 1. Get all 5 cells
        // 2. Count how many times the letter appears
        // 3. First Check: check all letters and see how many each letter is repeated
        // 4. Second check: normal check
        // 5. Check game status
        // 6. If game can continue, continue

        if (gameOver) return

        // 1. Get all 5 cells
        const guess = []
        for (let col = 0; col < 5; col++) {
            let cellsQuery = `.game-letter[data-row='${currentRow}'][data-col='${col}']`
            const cell = document.querySelector(cellsQuery)
            guess.push(cell.innerHTML)
        }

        // 2. Counting letters in magic word
        const letterCount = {}
        for (const letter of magicWord) {
            letterCount[letter] = (letterCount[letter] || 0) + 1
        }

        // 3. First check
        const isGreen = [false, false, false, false, false]

        for (let i = 0; i < 5; i++) {
            if (guess[i] === magicWord[i]) {
                isGreen[i] = true
                letterCount[guess[i]] = letterCount[guess[i]] - 1
                let correctCellQuery = `.game-letter[data-row="${currentRow}"][data-col="${i}"]`
                const correctCellElement = document.querySelector(correctCellQuery)
                correctCellElement.style = 'background-color: #43a047; color: white;'
            }
        }

        // 4. Second check
        let correctCount = 0

        for (let i = 0; i < 5; i++) {
            const letter = guess[i]
            let correctCellQuery = `.game-letter[data-row="${currentRow}"][data-col="${i}"]`
            const correctCellElement = document.querySelector(correctCellQuery)

            if (isGreen[i]) {
                correctCount++
                // updateKeyboard(letter, 'correct')
                continue;
            }

            if (letterCount[letter] > 0) {
                correctCellElement.style = 'background-color: #e4a81d; color: white;'
                letterCount[letter] = letterCount[letter] - 1
                // updateKeyboard(letter, 'misplaced')
            } else {
                correctCellElement.style = 'background-color: #757575; color: white;'

                // updateKeyboard(letter, 'absent')
            }
        }

        // 5. Check game status
        if (correctCount === 5) {
            gameOver = true;
            const points = getPoints(attemptsUsed);
            showEndScreen(
                'Félicitations !',
                `Tu as trouvé le mot en ${attemptsUsed} essai${attemptsUsed > 1 ? 's' : ''} !`,
                points
            );
            saveGameResult(points, 'victoire');
            return
        }

        // last row
        if (currentRow === 4) {
            gameOver = true
            showEndScreen(
                'Dommage !',
                'Tu n\'as pas trouvé cette fois.',
                0,
                `Le mot était : ${magicWord}`
            );
            saveGameResult(0, 'defaite');
            return
        }

        currentRow++;
        currentCol = 0;
    }


    function addLetter(letter) {
        // 1. If game ended, does nothing
        // 2. If currentCol === 5, does nothing
        // 3. If currentCol !== 5, adds letter (upperCase) in currentCol and currentRow
        // 4. Finally: columnCol++
    
        if (gameOver) return
        if (currentCol === 5) return

        const positionToChange = `.game-letter[data-row='${currentRow}'][data-col='${currentCol}']`
        const letterToChange = document.querySelector(positionToChange)
        letterToChange.innerHTML = letter
        
        currentCol++
    }

    function removeLetter() {
        // 1. If gameOver, return
        // 2. If currentCol === 0, return
        // 3. If not, then currentCol-- and remove letter
        if (gameOver) return
        if (currentCol === 0) return
        
        currentCol--

        const positionToChange = `.game-letter[data-row='${currentRow}'][data-col='${currentCol}']`
        const letterToChange = document.querySelector(positionToChange)
        letterToChange.innerHTML = ''
    }

</script>
</html>