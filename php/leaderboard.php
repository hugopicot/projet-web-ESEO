<?php
require "config.php";

// Récupération des filtres
$game = isset($_GET['game']) ? $_GET['game'] : 'mot-mele';
$period = isset($_GET['period']) ? $_GET['period'] : 'mois';

// Récupérer l'ID utilisateur connecté
$current_user_id = isset($_SESSION['id']) ? $_SESSION['id'] : null;

// Construction de la condition de jeu
if ($game === 'mot-mele') {
    $game_condition = "p.id_grille IS NOT NULL";
    $score_label = "temps";
    $order_direction = "ASC";
} else {
    $game_condition = "p.id_mot IS NOT NULL";
    $score_label = "essais";
    $order_direction = "ASC";
}

// Construction de la condition de période
switch ($period) {
    case 'jour':
        $period_condition = "DATE(p.date_fin) = CURDATE()";
        break;
    case 'semaine':
        $period_condition = "p.date_fin >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
        break;
    case 'mois':
    default:
        $period_condition = "p.date_fin >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
        break;
}

// Requête pour obtenir le classement
$sql = "
    SELECT 
        u.id_utilisateur,
        u.pseudo,
        u.avatar,
        MAX(p.score) as best_score
    FROM partie p
    INNER JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur
    WHERE {$game_condition} 
      AND p.date_fin IS NOT NULL
      AND {$period_condition}
    GROUP BY u.id_utilisateur, u.pseudo, u.avatar
    ORDER BY best_score {$order_direction}
    LIMIT 20
";

$stmt = $db->prepare($sql);
$stmt->execute();
$leaderboard = $stmt->fetchAll();

// Séparer podium et reste
$podium = array_slice($leaderboard, 0, 3);
$rest = array_slice($leaderboard, 3);

// Fonction pour formater le score
function formatScore($score, $game) {
    if ($game === 'mot-mele') {
        $minutes = floor($score / 60);
        $seconds = $score % 60;
        return sprintf("%d:%02d", $minutes, $seconds);
    }
    return $score . " points";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard - JeuxNova</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <style>
        .podium-section {
            display: flex;
            justify-content: center;
            align-items: flex-end;
            gap: 20px;
            margin: 40px 0;
        }
        .podium-item {
            text-align: center;
            transition: transform 0.3s ease;
        }
        .podium-item:hover {
            transform: translateY(-5px);
        }
        .podium-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #e5e5e5;
            background-color: #f0f0f0;
        }
        .podium-item.first .podium-avatar {
            width: 100px;
            height: 100px;
            border-color: #FFD700;
        }
        .podium-item.second .podium-avatar {
            border-color: #C0C0C0;
        }
        .podium-item.third .podium-avatar {
            border-color: #CD7F32;
        }
        .podium-rank {
            display: inline-block;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            line-height: 30px;
            font-weight: bold;
            color: white;
            margin-top: 10px;
        }
        .podium-item.first .podium-rank { background: #FFD700; color: #333; }
        .podium-item.second .podium-rank { background: #C0C0C0; color: #333; }
        .podium-item.third .podium-rank { background: #CD7F32; }
        .crown {
            font-size: 24px;
            margin-bottom: 5px;
        }
        .filter-btn {
            border-radius: 20px;
            padding: 8px 20px;
            margin: 0 5px;
            transition: all 0.2s;
        }
        .filter-btn.active {
            background-color: #000;
            color: white;
            border-color: #000;
        }
        .rank-row {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            transition: background 0.2s;
        }
        .rank-row:hover {
            background-color: #f8f9fa;
        }
        .rank-row.current-user {
            background-color: #e8f4ff;
        }
        .rank-number {
            width: 40px;
            font-weight: bold;
            font-size: 18px;
        }
        .rank-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 15px;
        }
        .rank-name {
            flex: 1;
            font-weight: 500;
        }
        .rank-score {
            font-weight: bold;
            color: #666;
        }
    </style>
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

<!-- CONTENU -->
<div class="container mt-4">
    <h1 class="text-center fw-bold mb-4">Leaderboard</h1>
    
    <!-- Filtres -->
    <div class="text-center mb-4">
        <div class="mb-3">
            <span class="text-muted me-2">Jeu :</span>
            <button class="btn btn-outline-dark filter-btn <?php echo $game === 'mot-mele' ? 'active' : ''; ?>" 
                    data-group="game" data-value="mot-mele">Mot mêlé</button>
            <button class="btn btn-outline-dark filter-btn <?php echo $game === 'mot-jour' ? 'active' : ''; ?>" 
                    data-group="game" data-value="mot-jour">Mot du jour</button>
        </div>
        <div>
            <span class="text-muted me-2">Période :</span>
            <button class="btn btn-outline-dark filter-btn <?php echo $period === 'jour' ? 'active' : ''; ?>" 
                    data-group="period" data-value="jour">Aujourd'hui</button>
            <button class="btn btn-outline-dark filter-btn <?php echo $period === 'semaine' ? 'active' : ''; ?>" 
                    data-group="period" data-value="semaine">Cette semaine</button>
            <button class="btn btn-outline-dark filter-btn <?php echo $period === 'mois' ? 'active' : ''; ?>" 
                    data-group="period" data-value="mois">Ce mois</button>
        </div>
    </div>

    <!-- Podium -->
    <?php if (count($podium) > 0): ?>
    <div class="podium-section">
        <?php 
        // Réorganiser: 2ème, 1er, 3ème
        $podium_order = [];
        if (isset($podium[1])) $podium_order[] = ['rank' => 2, 'data' => $podium[1], 'class' => 'second'];
        if (isset($podium[0])) $podium_order[] = ['rank' => 1, 'data' => $podium[0], 'class' => 'first'];
        if (isset($podium[2])) $podium_order[] = ['rank' => 3, 'data' => $podium[2], 'class' => 'third'];
        
        foreach ($podium_order as $item):
            $player = $item['data'];
            $avatarUrl = $player['avatar'] ? $player['avatar'] : "https://via.placeholder.com/100?text=" . substr($player['pseudo'], 0, 1);
        ?>
        <div class="podium-item <?php echo $item['class']; ?>">
            <?php if ($item['rank'] === 1): ?>
                <div class="crown">👑</div>
            <?php endif; ?>
            <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="Avatar" class="podium-avatar">
            <div class="podium-rank"><?php echo $item['rank']; ?></div>
            <div class="fw-bold mt-2"><?php echo htmlspecialchars($player['pseudo']); ?></div>
            <div class="text-muted"><?php echo formatScore($player['best_score'], $game); ?></div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <div class="text-center py-5">
        <p class="text-muted fs-5">Aucun résultat pour cette période.</p>
    </div>
    <?php endif; ?>

    <!-- Classement étendu -->
    <?php if (count($rest) > 0): ?>
    <div class="card border shadow-sm mt-4">
        <div class="card-body p-0">
            <?php foreach ($rest as $index => $player): 
                $rank = $index + 4;
                $isCurrentUser = ($current_user_id && $player['id_utilisateur'] == $current_user_id);
                $avatarUrl = $player['avatar'] ? $player['avatar'] : "https://via.placeholder.com/40?text=" . substr($player['pseudo'], 0, 1);
            ?>
            <div class="rank-row <?php echo $isCurrentUser ? 'current-user' : ''; ?>">
                <div class="rank-number"><?php echo $rank; ?></div>
                <img src="<?php echo htmlspecialchars($avatarUrl); ?>" alt="" class="rank-avatar">
                <div class="rank-name"><?php echo $isCurrentUser ? 'Vous' : htmlspecialchars($player['pseudo']); ?></div>
                <div class="rank-score"><?php echo formatScore($player['best_score'], $game); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php elseif (count($podium) >= 3): ?>
    <div class="text-center py-4">
        <p class="text-muted">Pas assez de joueurs pour afficher un classement étendu.</p>
    </div>
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
<script>
    // Gestion des filtres
    document.querySelectorAll('.filter-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            const group = btn.getAttribute('data-group');
            const value = btn.getAttribute('data-value');
            
            const params = new URLSearchParams(window.location.search);
            params.set(group, value);
            
            window.location.href = '?' + params.toString();
        });
    });
</script>
</body>
</html>
