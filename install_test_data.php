<?php
/**
 * Script d'installation des données de test
 * Exécute les requêtes SQL pour peupler la BDD
 */

require_once 'includes/db.php';

echo "<h1>Installation des données de test</h1>";
echo "<style>body { font-family: Arial, sans-serif; padding: 20px; } .success { color: green; } .error { color: red; } .info { color: blue; }</style>";

$errors = [];
$success = [];

// 1. Vérifier/ajouter les colonnes bio et avatar
echo "<h2>1. Vérification des colonnes</h2>";
try {
    $stmt = $pdo->query("DESCRIBE utilisateur");
    $columns = array_column($stmt->fetchAll(), 'Field');
    
    if (!in_array('bio', $columns)) {
        $pdo->exec("ALTER TABLE utilisateur ADD COLUMN bio TEXT NULL");
        $success[] = "Colonne 'bio' ajoutée";
    } else {
        echo "<p class='info'>ℹ️ Colonne 'bio' existe déjà</p>";
    }
    
    if (!in_array('avatar', $columns)) {
        $pdo->exec("ALTER TABLE utilisateur ADD COLUMN avatar VARCHAR(255) NULL");
        $success[] = "Colonne 'avatar' ajoutée";
    } else {
        echo "<p class='info'>ℹ️ Colonne 'avatar' existe déjà</p>";
    }
} catch (Exception $e) {
    $errors[] = "Erreur colonnes: " . $e->getMessage();
}

// 2. Insérer les utilisateurs
echo "<h2>2. Insertion des utilisateurs</h2>";
$users = [
    [1, 'test@nova.com', 'password123', 'NovaExplorer', 'joueur', 'Passionné de mots et de casse-têtes !'],
    [2, 'player2@nova.com', 'password123', 'WordMaster', 'joueur', 'Champion des mots mêlés'],
    [3, 'player3@nova.com', 'password123', 'PuzzleKing', 'joueur', 'Roi des puzzles'],
    [4, 'player4@nova.com', 'password123', 'QuickTyper', 'joueur', 'Rapide comme l\'éclair'],
    [5, 'player5@nova.com', 'password123', 'BrainStorm', 'joueur', 'Réflexion intense'],
];

$stmt = $pdo->prepare("INSERT IGNORE INTO utilisateur (id_utilisateur, email, mot_de_passe, pseudo, role, date_creation, bio) VALUES (?, ?, ?, ?, ?, NOW(), ?)");

foreach ($users as $user) {
    try {
        $stmt->execute($user);
        if ($stmt->rowCount() > 0) {
            $success[] = "Utilisateur '{$user[3]}' créé";
        } else {
            echo "<p class='info'>ℹ️ Utilisateur '{$user[3]}' existe déjà</p>";
        }
    } catch (Exception $e) {
        $errors[] = "Erreur utilisateur {$user[3]}: " . $e->getMessage();
    }
}

// 3. Insérer les parties
echo "<h2>3. Insertion des parties</h2>";
$parties = [
    // Mot Mêlé - aujourd'hui
    ['NOW()', 'NOW()', 45, 'termine', 1, 1, 'NULL'],
    ['NOW()', 'NOW()', 55, 'termine', 2, 1, 'NULL'],
    ['NOW()', 'NOW()', 70, 'termine', 3, 1, 'NULL'],
    ['NOW()', 'NOW()', 85, 'termine', 4, 1, 'NULL'],
    ['NOW()', 'NOW()', 120, 'termine', 5, 1, 'NULL'],
    // Mot du Jour - aujourd'hui
    ['NOW()', 'NOW()', 3, 'termine', 1, 'NULL', 1],
    ['NOW()', 'NOW()', 4, 'termine', 2, 'NULL', 1],
    ['NOW()', 'NOW()', 2, 'termine', 3, 'NULL', 1],
    ['NOW()', 'NOW()', 5, 'termine', 4, 'NULL', 1],
    ['NOW()', 'NOW()', 6, 'termine', 5, 'NULL', 1],
];

$partiesInserted = 0;
foreach ($parties as $p) {
    try {
        $sql = "INSERT INTO partie (date_debut, date_fin, score, statut, id_utilisateur, id_grille, id_mot) 
                VALUES ({$p[0]}, {$p[1]}, {$p[2]}, '{$p[3]}', {$p[4]}, {$p[5]}, {$p[6]})";
        $pdo->exec($sql);
        $partiesInserted++;
    } catch (Exception $e) {
        $errors[] = "Erreur partie: " . $e->getMessage();
    }
}
$success[] = "$partiesInserted parties insérées";

// Afficher résultats
echo "<h2>Résultat</h2>";
if (count($errors) > 0) {
    echo "<h3 class='error'>Erreurs:</h3><ul>";
    foreach ($errors as $e) echo "<li class='error'>❌ $e</li>";
    echo "</ul>";
}

if (count($success) > 0) {
    echo "<h3 class='success'>Succès:</h3><ul>";
    foreach ($success as $s) echo "<li class='success'>✅ $s</li>";
    echo "</ul>";
}

// Liens de test
echo "<h2>Testez maintenant :</h2>";
echo "<p><a href='pages/profil.php?id=1' target='_blank'>👤 Page Profil (ID=1)</a></p>";
echo "<p><a href='pages/leaderboard.php' target='_blank'>🏆 Page Leaderboard</a></p>";
echo "<p><a href='diagnostic.php' target='_blank'>🔧 Diagnostic</a></p>";

echo "<hr><p><em>Ce fichier peut être supprimé après l'installation.</em></p>";
?>
