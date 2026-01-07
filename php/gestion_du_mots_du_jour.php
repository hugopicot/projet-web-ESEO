<?php

// =========================================
// CONNEXION A LA BASE
// =========================================
require_once __DIR__ . '/../includes/db.php';


// Ajouter un mot du jour

if(isset($_POST['ajouter'])){
    $mot = $_POST['mot'];
    $definition = $_POST['definition'];
    $id_utilisateur = $_POST['id_utilisateur'];

   $stmt = $pdo->prepare(
    "INSERT INTO motdujour (mot, definition, id_utilisateur,date)
     VALUES (?, ?, ?,NOW())"
);
$stmt->execute([$mot, $definition, $id_utilisateur]);

}


// Modifier un mot du jour

if(isset($_POST['modifier'])){
    $id_mot = $_POST['id_mot'];
    $mot = $_POST['mot'];
    $definition = $_POST['definition'];

    $stmt = $pdo->prepare("UPDATE motdujour SET mot = ?, definition = ? WHERE id_mot = ?");
    $stmt->execute([$mot, $definition, $id_mot]);
}


// Supprimer un mot du jour

if(isset($_POST['supprimer'])){
    $id_mot = $_POST['id_mot'];
    $stmt = $pdo->prepare("DELETE FROM motdujour WHERE id_mot = ?");
    $stmt->execute([$id_mot]);
}


// Récupérer tous les mots

$stmt = $pdo->query("SELECT * FROM motdujour ORDER BY date DESC");
$mots = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion du Mot du Jour</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { margin-bottom: 20px; padding: 10px; background: #f4f4f4; border-radius: 5px; }
        input, textarea { width: 100%; margin: 5px 0; padding: 8px; }
        button { padding: 8px 12px; margin-top: 5px; }
        hr { margin: 20px 0; }
        small { color: #555; }
    </style>
</head>
<body>

<h1>Gestion du Mot du Jour</h1>

<!-- Formulaire pour ajouter un mot -->
<h2>Ajouter un nouveau mot</h2>
<form method="post">
    <input type="text" name="mot" placeholder="Mot" required>
    <input type="number" name="id_utilisateur" placeholder="ID utilisateur" required>
    <textarea name="definition" placeholder="Définition" required></textarea>
    <button type="submit" name="ajouter">Ajouter</button>
</form>

<hr>

<h2>Mots existants</h2>
<?php if(count($mots) == 0): ?>
    <p>Aucun mot du jour pour l’instant.</p>
<?php endif; ?>

<?php foreach($mots as $m): ?>
    <form method="post">
        <input type="hidden" name="id_mot" value="<?= $m['id_mot'] ?>">
        <input type="text" name="mot" value="<?= htmlspecialchars($m['mot']) ?>" required>
        <textarea name="definition" required><?= htmlspecialchars($m['definition']) ?></textarea>
        <small>ID Utilisateur: <?= $m['id_utilisateur'] ?> | Date: <?= $m['date'] ?></small><br>
        <button type="submit" name="modifier">Modifier</button>
        <button type="submit" name="supprimer">Supprimer</button>
    </form>
    <hr>
<?php endforeach; ?>

</body>
</html>
