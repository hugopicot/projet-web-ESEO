<?php
$host = "localhost";
$dbname = "bdd_nova";
$username = "root";
$password = "";

try {
    $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Erreur connexion BDD : " . $e->getMessage());
}

session_start();
?>
