<?php
require_once '../includes/db.php';

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 1;
$message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_pseudo = trim($_POST['pseudo']);
    $new_bio = trim($_POST['bio']);
    
    try {
        $stmt = $pdo->prepare("UPDATE utilisateur SET pseudo = ?, bio = ? WHERE id_utilisateur = ?");
        $stmt->execute([$new_pseudo, $new_bio, $user_id]);
        
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $fileName = $_FILES['avatar']['name'];
            $fileSize = $_FILES['avatar']['size'];
            $fileType = $_FILES['avatar']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadDir . $newFileName;
                
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $webPath = '/projet-web-ESEO/assets/uploads/' . $newFileName;
                    $stmt = $pdo->prepare("UPDATE utilisateur SET avatar = ? WHERE id_utilisateur = ?");
                    $stmt->execute([$webPath, $user_id]);
                }
            }
        }
        
        $message = "Profil mis à jour !";
    } catch (PDOException $e) {
        $message = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

$stmt = $pdo->prepare("SELECT pseudo, bio, avatar, date_creation FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $user = ['pseudo' => 'User_name', 'bio' => '', 'avatar' => '', 'date_creation' => '2025-01-01'];
}

$avatarPath = $user['avatar'] ? $user['avatar'] : "data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='100%25' height='100%25' viewBox='0 0 100 100'%3E%3Crect width='100' height='100' fill='%23D9D9D9'/%3E%3C/svg%3E";

$stmt = $pdo->prepare("SELECT AVG(score) as moy_temps FROM partie WHERE id_utilisateur = ? AND id_grille IS NOT NULL");
$stmt->execute([$user_id]);
$res = $stmt->fetch();
$moy_temps_sec = $res['moy_temps'] ? round($res['moy_temps']) : 0;
$minutes = floor($moy_temps_sec / 60);
$secondes = $moy_temps_sec % 60;
$temps_moyen_str = sprintf("%02d:%02d", $minutes, $secondes);

$stmt = $pdo->prepare("SELECT AVG(score) as moy_essais FROM partie WHERE id_utilisateur = ? AND id_mot IS NOT NULL");
$stmt->execute([$user_id]);
$res = $stmt->fetch();
$moy_essais = $res['moy_essais'] ? round($res['moy_essais'], 1) : 0;

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM partie WHERE id_utilisateur = ?");
$stmt->execute([$user_id]);
$res = $stmt->fetch();
$total_parties = $res['total'];
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$stmt = $pdo->prepare("SELECT COUNT(*) FROM partie WHERE id_utilisateur = ? AND date_fin IS NOT NULL");
$stmt->execute([$user_id]);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);

$stmt = $pdo->prepare("
    SELECT p.date_fin, p.score, p.id_grille, p.id_mot, 
           CASE 
               WHEN p.id_grille IS NOT NULL THEN 'Mot mêlé'
               WHEN p.id_mot IS NOT NULL THEN 'Mot du jour'
               ELSE 'Jeu inconnu'
           END as nom_jeu,
           CASE
               WHEN p.id_grille IS NOT NULL THEN 'Niveau facile' -- Placeholder, info non dispo en jointure simple sans table grille
               WHEN p.id_mot IS NOT NULL THEN 'Quotidien'
               ELSE '-'
           END as detail
    FROM partie p
    WHERE p.id_utilisateur = ? AND p.date_fin IS NOT NULL
    ORDER BY p.date_fin DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt = $pdo->prepare("
    SELECT p.date_fin, p.score, p.id_grille, p.id_mot, 
           CASE 
               WHEN p.id_grille IS NOT NULL THEN 'Mot mêlé'
               WHEN p.id_mot IS NOT NULL THEN 'Mot du jour'
               ELSE 'Jeu inconnu'
           END as nom_jeu,
           CASE
               WHEN p.id_grille IS NOT NULL THEN 'Niveau facile'
               WHEN p.id_mot IS NOT NULL THEN 'Quotidien'
               ELSE '-'
           END as detail
    FROM partie p
    WHERE p.id_utilisateur = :user_id AND p.date_fin IS NOT NULL
    ORDER BY p.date_fin DESC
    LIMIT :limit OFFSET :offset
");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$historique = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - <?php echo htmlspecialchars($user['pseudo']); ?></title>
    <link rel="stylesheet" href="../css/profil.css">
</head>
<body>

    <?php include '../includes/header.php'; ?>

    <div class="container">
        
        <?php if (!empty($message)): ?>
            <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border-radius: 5px; border: 1px solid #c3e6cb;">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="profile-header">
            <div class="avatar-container">
                <img src="<?php echo htmlspecialchars($avatarPath); ?>" alt="Avatar">
            </div>
            <div class="profile-info">
                <h1><?php echo htmlspecialchars($user['pseudo']); ?></h1>
                <p><?php echo nl2br(htmlspecialchars($user['bio'] ?? '')); ?></p>
            </div>
            
            <button class="edit-button" onclick="openModal()">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" stroke="#666" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>
        </div>

        <section class="stats-section">
            <h2>Statistiques</h2>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">Mots mêlé</div>
                    <div class="stat-value">Temps moyen <span><?php echo $temps_moyen_str; ?></span></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">Mots du jour</div>
                    <div class="stat-value">Essais moyen <span><?php echo $moy_essais; ?></span></div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">Parties jouées</div>
                    <div class="stat-value"><span><?php echo $total_parties; ?></span></div>
                </div>
            </div>
        </section>

        <section class="history-section">
            <h2>Historique</h2>
            <table class="history-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Jeu</th>
                        <th>Score</th>
                        <th>Détail</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($historique) > 0): ?>
                        <?php foreach ($historique as $partie): ?>
                            <tr>
                                <td><?php echo date('d/m/Y', strtotime($partie['date_fin'])); ?></td>
                                <td><?php echo htmlspecialchars($partie['nom_jeu']); ?></td>
                                <td>
                                    <?php 
                                    if ($partie['nom_jeu'] == 'Mot mêlé') {
                                        echo gmdate("i:s", $partie['score']);
                                    } else {
                                        echo $partie['score'];
                                    }
                                    ?>
                                </td>
                                <td><?php echo htmlspecialchars($partie['detail']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" style="text-align:center; color:#888;">Aucune partie jouée pour le moment.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
            
            <?php if ($total_pages > 1): ?>
                <div style="margin-top: 20px; text-align: center;">
                    <?php if ($page > 1): ?>
                        <a href="?id=<?php echo $user_id; ?>&page=<?php echo $page - 1; ?>" style="text-decoration: none; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; color: #333;">&laquo; Précédent</a>
                    <?php endif; ?>

                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?id=<?php echo $user_id; ?>&page=<?php echo $i; ?>" style="text-decoration: none; padding: 5px 10px; border: 1px solid <?php echo ($i == $page) ? '#000' : '#ccc'; ?>; border-radius: 4px; color: <?php echo ($i == $page) ? '#fff' : '#333'; ?>; background-color: <?php echo ($i == $page) ? '#000' : 'transparent'; ?>; margin: 0 2px;">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?id=<?php echo $user_id; ?>&page=<?php echo $page + 1; ?>" style="text-decoration: none; padding: 5px 10px; border: 1px solid #ccc; border-radius: 4px; color: #333;">Suivant &raquo;</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </section>

    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2>Modifier le profil</h2>
            <form method="POST" action="" enctype="multipart/form-data">
                <input type="hidden" name="update_profile" value="1">
                <div class="form-group">
                    <label for="pseudo">Nom d'utilisateur</label>
                    <input type="text" id="pseudo" name="pseudo" value="<?php echo htmlspecialchars($user['pseudo']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="bio">Bio</label>
                    <textarea id="bio" name="bio" rows="4"><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                <div class="form-group">
                    <label for="avatar">Avatar</label>
                    <input type="file" id="avatar" name="avatar" accept="image/*">
                </div>
                <button type="submit" class="btn-save">Enregistrer</button>
            </form>
        </div>
    </div>

    <script>
        const modal = document.getElementById('editModal');

        function openModal() {
            modal.style.display = 'block';
        }

        function closeModal() {
            modal.style.display = 'none';
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                closeModal();
            }
        }

        const avatarInput = document.getElementById('avatar');
        const avatarPreview = document.createElement('img');
        avatarPreview.style.maxWidth = '100px';
        avatarPreview.style.marginTop = '10px';
        avatarPreview.style.borderRadius = '50%';
        avatarPreview.style.display = 'none';
        
        avatarInput.parentNode.appendChild(avatarPreview);

        avatarInput.addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    avatarPreview.src = e.target.result;
                    avatarPreview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                avatarPreview.style.display = 'none';
            }
        });
    </script>

</body>
</html>
