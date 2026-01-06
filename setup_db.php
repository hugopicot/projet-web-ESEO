<?php
// Script d'installation de la base de données
$host = '127.0.0.1';
$username = 'root';
$password = '';

try {
    // 1. Connexion sans base de données
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // 2. Création de la base de données
    echo "Création de la base de données 'bdd_nova'...\n";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `bdd_nova` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci");
    echo "Base de données créée (ou déjà existante).\n";

    // 3. Sélection de la base
    $pdo->exec("USE `bdd_nova`");

    // 4. Lecture du fichier SQL
    $sqlFile = __DIR__ . '/bdd_nova.sql';
    if (!file_exists($sqlFile)) {
        die("Erreur : Le fichier bdd_nova.sql est introuvable dans " . __DIR__);
    }
    
    $sql = file_get_contents($sqlFile);

    // 5. Exécution des requêtes SQL
    echo "Importation des tables...\n";
    $pdo->exec($sql);
    
    echo "Succès ! La base de données a été installée correctement.\n";
    echo "Vous pouvez maintenant accéder au site.";

} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>
