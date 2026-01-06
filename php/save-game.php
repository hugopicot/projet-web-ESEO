<?php
header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "bdd_nova";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>