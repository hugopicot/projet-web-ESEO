<?php
header('Content-Type: application/json');

// =========================================
// CONNEXION A LA BASE
// =========================================
require_once __DIR__ . '/../includes/db.php';
$conn = $pdo; 

    $input = json_decode(file_get_contents('php://input'), true);

    $partie_id = $input['partie_id'] ?? null;
    $score     = $input['score'] ?? 0;
    $statut    = $input['statut'] ?? 'defaite';

    if (!$partie_id) {
        echo json_encode(['success' => false, 'message' => 'ID de partie manquant']);
        exit;
    }

    $stmt = $conn->prepare("
        UPDATE partie 
        SET date_fin = NOW(), 
            score = :score, 
            statut = :statut 
        WHERE id_partie = :id_partie
    ");
    $stmt->bindParam(':score', $score, PDO::PARAM_INT);
    $stmt->bindParam(':statut', $statut);
    $stmt->bindParam(':id_partie', $partie_id, PDO::PARAM_INT);
    $stmt->execute();

    echo json_encode(['success' => true]);

?>