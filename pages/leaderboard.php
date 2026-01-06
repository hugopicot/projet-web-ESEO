<?php
require_once '../includes/db.php';

// Récupération des filtres
$game = isset($_GET['game']) ? $_GET['game'] : 'mot-mele';
$period = isset($_GET['period']) ? $_GET['period'] : 'mois';

// Récupérer l'ID utilisateur connecté (si session active)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

// Construction de la condition de jeu
if ($game === 'mot-mele') {
    $game_condition = "p.id_grille IS NOT NULL";
    $score_label = "temps"; // Plus bas = meilleur
    $order_direction = "ASC";
} else {
    $game_condition = "p.id_mot IS NOT NULL";
    $score_label = "essais"; // Plus bas = meilleur
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

// Requête pour obtenir le classement (meilleur score par utilisateur)
$sql = "
    SELECT 
        u.id_utilisateur,
        u.pseudo,
        u.avatar,
        MIN(p.score) as best_score
    FROM partie p
    INNER JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur
    WHERE {$game_condition} 
      AND p.date_fin IS NOT NULL
      AND {$period_condition}
    GROUP BY u.id_utilisateur, u.pseudo, u.avatar
    ORDER BY best_score {$order_direction}
    LIMIT 20
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$leaderboard = $stmt->fetchAll();

// Séparer podium (top 3) et reste du classement
$podium = array_slice($leaderboard, 0, 3);
$rest = array_slice($leaderboard, 3);

// Fonction pour formater le score
function formatScore($score, $game) {
    if ($game === 'mot-mele') {
        $minutes = floor($score / 60);
        $seconds = $score % 60;
        return sprintf("%d:%02d", $minutes, $seconds);
    }
    return $score . " essais";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Leaderboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../css/leaderboard.css">
</head>
<body>
    <?php include '../includes/header.php'; ?>
    <main class="leaderboard-page">
        <div class="leaderboard-header">
            <h1 class="leaderboard-title">Leaderboard</h1>
        </div>
        <div class="filters">
            <div class="filter">
                <div class="filter-label">Jeu</div>
                <div class="segmented" role="group" aria-label="Jeu">
                    <button class="option" type="button" aria-pressed="<?php echo $game === 'mot-mele' ? 'true' : 'false'; ?>" data-group="game" data-value="mot-mele">Mot mêlé</button>
                    <button class="option" type="button" aria-pressed="<?php echo $game === 'mot-jour' ? 'true' : 'false'; ?>" data-group="game" data-value="mot-jour">Mot du jour</button>
                </div>
            </div>
            <div class="filter">
                <div class="filter-label">Période</div>
                <div class="segmented" role="group" aria-label="Période">
                    <button class="option" type="button" aria-pressed="<?php echo $period === 'jour' ? 'true' : 'false'; ?>" data-group="period" data-value="jour">Jour</button>
                    <button class="option" type="button" aria-pressed="<?php echo $period === 'semaine' ? 'true' : 'false'; ?>" data-group="period" data-value="semaine">Semaine</button>
                    <button class="option" type="button" aria-pressed="<?php echo $period === 'mois' ? 'true' : 'false'; ?>" data-group="period" data-value="mois">Mois</button>
                </div>
            </div>
        </div>
        
        <?php if (count($podium) > 0): ?>
        <section class="podium">
            <?php 
            // Réorganiser: 2ème, 1er, 3ème pour l'affichage visuel
            $podium_order = [];
            if (isset($podium[1])) $podium_order[] = ['rank' => 2, 'data' => $podium[1]];
            if (isset($podium[0])) $podium_order[] = ['rank' => 1, 'data' => $podium[0], 'center' => true];
            if (isset($podium[2])) $podium_order[] = ['rank' => 3, 'data' => $podium[2]];
            
            foreach ($podium_order as $item):
                $player = $item['data'];
                $isCenter = isset($item['center']) && $item['center'];
                $avatarUrl = $player['avatar'] ? $player['avatar'] : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100' height='100' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%23D9D9D9'/%3E%3C/svg%3E";
            ?>
            <div class="podium-item<?php echo $isCenter ? ' center' : ''; ?>">
                <?php if ($isCenter): ?>
                <img class="crown" src="../.figma/image/mih59k6t-rr6k5bn.svg" alt="Couronne">
                <?php endif; ?>
                <div class="circle" style="background-image: url('<?php echo htmlspecialchars($avatarUrl); ?>'); background-size: cover; background-position: center;"></div>
                <div class="badge"><?php echo $item['rank']; ?></div>
            </div>
            <?php endforeach; ?>
        </section>
        <section class="podium-labels">
            <?php foreach ($podium_order as $item): ?>
            <div>
                <div class="name"><?php echo htmlspecialchars($item['data']['pseudo']); ?></div>
                <div class="points"><?php echo formatScore($item['data']['best_score'], $game); ?></div>
            </div>
            <?php endforeach; ?>
        </section>
        <?php else: ?>
        <section class="podium" style="justify-content: center; padding: 60px 0;">
            <p style="text-align: center; color: #888;">Aucun résultat pour cette période.</p>
        </section>
        <?php endif; ?>
        
        <section class="board">
            <?php if (count($rest) > 0): ?>
                <?php foreach ($rest as $index => $player): 
                    $rank = $index + 4;
                    $isCurrentUser = ($current_user_id && $player['id_utilisateur'] == $current_user_id);
                ?>
                <div class="row<?php echo $isCurrentUser ? ' active' : ''; ?>">
                    <div class="rank">
                        <div class="num"><?php echo $rank; ?></div>
                        <div class="dot" style="<?php echo $player['avatar'] ? "background-image: url('" . htmlspecialchars($player['avatar']) . "'); background-size: cover;" : ''; ?>"></div>
                    </div>
                    <div class="user"><?php echo $isCurrentUser ? 'Vous' : htmlspecialchars($player['pseudo']); ?></div>
                    <div class="pts"><?php echo formatScore($player['best_score'], $game); ?></div>
                </div>
                <?php endforeach; ?>
            <?php elseif (count($podium) >= 3): ?>
                <p style="text-align: center; color: #888; padding: 20px;">Pas assez de joueurs pour afficher un classement étendu.</p>
            <?php endif; ?>
        </section>
    </main>
    <script>
      // Récupérer les filtres actuels
      const currentParams = new URLSearchParams(window.location.search);
      const currentGame = currentParams.get('game') || 'mot-mele';
      const currentPeriod = currentParams.get('period') || 'mois';

      document.querySelectorAll('.segmented .option').forEach(function(btn){
        btn.addEventListener('click', function(){
          const group = btn.getAttribute('data-group');
          const value = btn.getAttribute('data-value');
          
          // Mettre à jour les paramètres
          const params = new URLSearchParams(window.location.search);
          params.set(group, value);
          
          // Recharger la page avec les nouveaux filtres
          window.location.href = '?' + params.toString();
        });
      });
    </script>
</body>
</html>

