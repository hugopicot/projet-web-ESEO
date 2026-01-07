<?php
header('Content-Type: application/json');

try {
    include '../includes/db.php';

    $stmt = $pdo->query("SELECT UPPER(mot) as mot FROM motdujour ORDER BY RAND() LIMIT 12");
    $words = $stmt->fetchAll(PDO::FETCH_COLUMN);

    echo json_encode([
        'success' => true,
        'words' => $words
    ]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Erreur lors de la récupération des mots: ' . $e->getMessage()
    ]);
}
?>
