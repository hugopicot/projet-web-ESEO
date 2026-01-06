<?php
/**
 * Script de diagnostic pour le projet Nova
 * Vérifie l'état de la BDD et aide à identifier les problèmes
 */

require_once 'includes/db.php';

echo "<h1>Diagnostic de la Base de Données</h1>";
echo "<style>body { font-family: Arial, sans-serif; padding: 20px; } .success { color: green; } .error { color: red; } .warning { color: orange; } pre { background: #f4f4f4; padding: 10px; border-radius: 5px; }</style>";

// 1. Vérifier la connexion
echo "<h2>1. Connexion à la BDD</h2>";
try {
    $stmt = $pdo->query("SELECT DATABASE() as db");
    $result = $stmt->fetch();
    echo "<p class='success'>✅ Connecté à la base : <strong>" . $result['db'] . "</strong></p>";
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur de connexion : " . $e->getMessage() . "</p>";
    exit;
}

// 2. Vérifier la structure de la table utilisateur
echo "<h2>2. Structure de la table 'utilisateur'</h2>";
try {
    $stmt = $pdo->query("DESCRIBE utilisateur");
    $columns = $stmt->fetchAll();
    echo "<pre>";
    foreach ($columns as $col) {
        echo $col['Field'] . " (" . $col['Type'] . ")";
        if ($col['Field'] === 'bio' || $col['Field'] === 'avatar') {
            echo " ✅";
        }
        echo "\n";
    }
    echo "</pre>";
    
    // Vérifier si bio et avatar existent
    $columnNames = array_column($columns, 'Field');
    if (!in_array('bio', $columnNames)) {
        echo "<p class='error'>❌ Colonne 'bio' manquante ! Exécutez : ALTER TABLE utilisateur ADD COLUMN bio TEXT NULL;</p>";
    }
    if (!in_array('avatar', $columnNames)) {
        echo "<p class='error'>❌ Colonne 'avatar' manquante ! Exécutez : ALTER TABLE utilisateur ADD COLUMN avatar VARCHAR(255) NULL;</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Table 'utilisateur' non trouvée : " . $e->getMessage() . "</p>";
}

// 3. Données dans la table utilisateur
echo "<h2>3. Utilisateurs dans la BDD</h2>";
try {
    $stmt = $pdo->query("SELECT id_utilisateur, pseudo, email, bio, avatar FROM utilisateur LIMIT 10");
    $users = $stmt->fetchAll();
    
    if (count($users) === 0) {
        echo "<p class='warning'>⚠️ Aucun utilisateur trouvé dans la table !</p>";
        echo "<p>Pour créer un utilisateur de test, exécutez :</p>";
        echo "<pre>INSERT INTO utilisateur (email, mot_de_passe, pseudo, role, date_creation, bio) 
VALUES ('test@nova.com', 'password123', 'NovaExplorer', 'joueur', NOW(), 'Passionné de mots!');</pre>";
    } else {
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Pseudo</th><th>Email</th><th>Bio</th><th>Avatar</th></tr>";
        foreach ($users as $u) {
            echo "<tr>";
            echo "<td>" . $u['id_utilisateur'] . "</td>";
            echo "<td>" . htmlspecialchars($u['pseudo']) . "</td>";
            echo "<td>" . htmlspecialchars($u['email']) . "</td>";
            echo "<td>" . (empty($u['bio']) ? '<em>vide</em>' : htmlspecialchars(substr($u['bio'], 0, 50))) . "</td>";
            echo "<td>" . (empty($u['avatar']) ? '<em>vide</em>' : '✅') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        echo "<p class='success'>✅ " . count($users) . " utilisateur(s) trouvé(s)</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur : " . $e->getMessage() . "</p>";
}

// 4. Données dans la table partie
echo "<h2>4. Parties dans la BDD</h2>";
try {
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM partie");
    $result = $stmt->fetch();
    echo "<p>Total parties : <strong>" . $result['total'] . "</strong></p>";
    
    if ($result['total'] > 0) {
        $stmt = $pdo->query("SELECT p.id_partie, p.score, p.date_fin, p.id_grille, p.id_mot, u.pseudo 
                             FROM partie p 
                             LEFT JOIN utilisateur u ON p.id_utilisateur = u.id_utilisateur 
                             ORDER BY p.date_fin DESC LIMIT 5");
        $parties = $stmt->fetchAll();
        echo "<table border='1' cellpadding='8' cellspacing='0'>";
        echo "<tr><th>ID</th><th>Joueur</th><th>Score</th><th>Date</th><th>Type</th></tr>";
        foreach ($parties as $p) {
            $type = $p['id_grille'] ? 'Mot mêlé' : ($p['id_mot'] ? 'Mot du jour' : '?');
            echo "<tr>";
            echo "<td>" . $p['id_partie'] . "</td>";
            echo "<td>" . htmlspecialchars($p['pseudo'] ?? 'Inconnu') . "</td>";
            echo "<td>" . $p['score'] . "</td>";
            echo "<td>" . $p['date_fin'] . "</td>";
            echo "<td>" . $type . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p class='warning'>⚠️ Aucune partie trouvée. Exécutez data_test.sql pour ajouter des données de test.</p>";
    }
} catch (Exception $e) {
    echo "<p class='error'>❌ Erreur : " . $e->getMessage() . "</p>";
}

// 5. Actions rapides
echo "<h2>5. Actions rapides</h2>";
echo "<p><a href='pages/profil.php?id=1'>🧑 Voir le profil ID=1</a></p>";
echo "<p><a href='pages/leaderboard.php'>🏆 Voir le leaderboard</a></p>";

// Test de mise à jour
echo "<h2>6. Test de mise à jour (ID=1)</h2>";
if (isset($_GET['test_update'])) {
    try {
        $stmt = $pdo->prepare("UPDATE utilisateur SET pseudo = 'TestUpdate_" . time() . "' WHERE id_utilisateur = 1");
        $stmt->execute();
        $rowCount = $stmt->rowCount();
        
        if ($rowCount > 0) {
            echo "<p class='success'>✅ Mise à jour réussie ! $rowCount ligne(s) affectée(s).</p>";
        } else {
            echo "<p class='error'>❌ 0 lignes affectées. L'utilisateur ID=1 n'existe probablement pas.</p>";
        }
    } catch (Exception $e) {
        echo "<p class='error'>❌ Erreur SQL : " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p><a href='?test_update=1'>🔧 Cliquez ici pour tester la mise à jour de l'utilisateur ID=1</a></p>";
}
?>
