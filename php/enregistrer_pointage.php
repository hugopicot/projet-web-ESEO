<?php
session_start();

header('Content-Type: application/json');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION["id"])) {
    http_response_code(401);
    echo json_encode(['erreur' => 'Utilisateur non authentifié']);
    exit;
}

// Obtenir les données de la requête
$donnees = json_decode(file_get_contents('php://input'), true);

if (!isset($donnees['pointage']) || !isset($donnees['talle']) || !isset($donnees['temps'])) {
    http_response_code(400);
    echo json_encode(['erreur' => 'Données incomplètes']);
    exit;
}

$id_utilisateur = $_SESSION["id"];
$pointage = (int)$donnees['pointage'];
$talle = (int)$donnees['talle'];
$temps = (int)$donnees['temps'];
$id_grille = isset($donnees['id_grille']) && !empty($donnees['id_grille']) ? (int)$donnees['id_grille'] : null;

try {
    require_once __DIR__ . '/../includes/db.php';
    $conn = $pdo;

    // Créer une nouvelle partie
    $stmt = $conn->prepare("
        INSERT INTO partie (date_debut, date_fin, score, statut, id_utilisateur, id_grille)
        VALUES (NOW(), NOW(), :pointage, 'completed', :id_utilisateur, :id_grille)
    ");

    $stmt->bindParam(':pointage', $pointage, PDO::PARAM_INT);
    $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $stmt->bindParam(':id_grille', $id_grille, PDO::PARAM_INT);

    $stmt->execute();

    $id_partie = $conn->lastInsertId();

    // Enregistrer dans le leaderboard
    $stmt_leaderboard = $conn->prepare("
        INSERT INTO leaderboard (score, date_entree, id_partie)
        VALUES (:pointage, NOW(), :id_partie)
    ");

    $stmt_leaderboard->bindParam(':pointage', $pointage, PDO::PARAM_INT);
    $stmt_leaderboard->bindParam(':id_partie', $id_partie, PDO::PARAM_INT);

    $stmt_leaderboard->execute();

    echo json_encode([
        'succes' => true,
        'id_partie' => $id_partie,
        'pointage' => $pointage,
        'message' => 'Pointage enregistré avec succès'
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['erreur' => 'Erreur dans la base de données: ' . $e->getMessage()]);
    exit;
}