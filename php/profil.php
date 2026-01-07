<?php
require "config.php";

$user_id = isset($_GET['id']) ? intval($_GET['id']) : ($_SESSION['id'] ?? 1);
$message = "";

// Traitement du formulaire de mise à jour
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $new_pseudo = trim($_POST['pseudo']);
    $new_bio = trim($_POST['bio']);

    
    try {
        $stmt = $db->prepare("UPDATE utilisateur SET pseudo = ?, bio = ? WHERE id_utilisateur = ?");
        $stmt->execute([$new_pseudo, $new_bio, $user_id]);
        
        // Traitement de l'upload d'avatar
        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = '../assets/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileTmpPath = $_FILES['avatar']['tmp_name'];
            $fileName = $_FILES['avatar']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            $allowedfileExtensions = array('jpg', 'gif', 'png', 'jpeg', 'webp');
            if (in_array($fileExtension, $allowedfileExtensions)) {
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadDir . $newFileName;
                
                if(move_uploaded_file($fileTmpPath, $dest_path)) {
                    $webPath = '/projet-web-ESEO/assets/uploads/' . $newFileName;
                    $stmt = $db->prepare("UPDATE utilisateur SET avatar = ? WHERE id_utilisateur = ?");
                    $stmt->execute([$webPath, $user_id]);
                }
            }
        }
        
        $message = "Profil mis à jour avec succès !";
    } catch (PDOException $e) {
        $message = "Erreur lors de la mise à jour : " . $e->getMessage();
    }
}

// Récupération des données utilisateur
$stmt = $db->prepare("SELECT pseudo, bio, avatar, date_creation FROM utilisateur WHERE id_utilisateur = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    $user = ['pseudo' => 'Utilisateur', 'bio' => '', 'avatar' => '', 'date_creation' => '2025-01-01'];
}

$avatarPath = $user['avatar'] ? $user['avatar'] : "https://via.placeholder.com/120?text=Avatar";

// Statistiques - Temps moyen mots mêlés
$stmt = $db->prepare("SELECT AVG(score) as moy_temps FROM partie WHERE id_utilisateur = ? AND id_grille IS NOT NULL");
$stmt->execute([$user_id]);
$res = $stmt->fetch();
$moy_temps_sec = $res['moy_temps'] ? round($res['moy_temps']) : 0;
$minutes = floor($moy_temps_sec / 60);
$secondes = $moy_temps_sec % 60;
$temps_moyen_str = sprintf("%02d:%02d", $minutes, $secondes);

// Statistiques - Essais moyens mot du jour
$stmt = $db->prepare("SELECT AVG(score) as moy_essais FROM partie WHERE id_utilisateur = ? AND id_mot IS NOT NULL");
$stmt->execute([$user_id]);
$res = $stmt->fetch();
$moy_essais = $res['moy_essais'] ? round($res['moy_essais'], 1) : 0;

// Statistiques - Total parties
$stmt = $db->prepare("SELECT COUNT(*) as total FROM partie WHERE id_utilisateur = ?");
$stmt->execute([$user_id]);
$res = $stmt->fetch();
$total_parties = $res['total'];

// Pagination historique
$limit = 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($page - 1) * $limit;

$stmt = $db->prepare("SELECT COUNT(*) FROM partie WHERE id_utilisateur = ? AND date_fin IS NOT NULL");
$stmt->execute([$user_id]);
$total_items = $stmt->fetchColumn();
$total_pages = ceil($total_items / $limit);

// Historique des parties
$stmt = $db->prepare("
    SELECT p.date_fin, p.statut, p.score, p.id_grille, p.id_mot, 
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
</head>
<body class="bg-white text-dark">

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom position-relative">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img src="../assets/logo.png" alt="Logo" height="40" class="me-2">
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navMenu">
            <ul class="navbar-nav navbar-nav-centered">
                <li class="nav-item"><a class="nav-link fw-semibold" href="mots.php">Jeux</a></li>
                <li class="nav-item"><a class="nav-link fw-semibold" href="leaderboard.php">Leaderboard</a></li>
            </ul>

            <div class="d-flex ms-auto nav-auth">
                <?php if (isset($_SESSION["id"])): ?>
                    <a class="btn btn-outline-dark me-2" href="admin.php">Admin</a>
                    <a class="btn btn-outline-dark me-2" href="profil.php">Mon Profil</a>
                    <a class="btn btn-dark" href="deconexion.php">Déconnexion</a>
                <?php else: ?>
                    <a class="btn btn-outline-dark me-2" href="account.php?mode=login">Connexion</a>
                    <a class="btn btn-dark" href="account.php?mode=register">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

<!-- CONTENU -->
<div class="container mt-5">
    
    <?php if (!empty($message)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo htmlspecialchars($message); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Profile Header -->
    <div class="row mb-5 align-items-center">
        <div class="col-auto">
            <img src="<?php echo htmlspecialchars($avatarPath); ?>" 
                 class="rounded-circle border shadow" 
                 width="120" height="120" 
                 style="object-fit: cover;"
                 alt="Avatar">
        </div>
        <div class="col">
            <h1 class="fw-bold mb-2"><?php echo htmlspecialchars($user['pseudo']); ?></h1>
            <p class="text-secondary mb-0"><?php echo nl2br(htmlspecialchars($user['bio'] ?? 'Aucune bio définie.')); ?></p>
        </div>
        <div class="col-auto">
            <button class="btn btn-outline-dark" data-bs-toggle="modal" data-bs-target="#editModal">
                ✏️ Modifier le profil
            </button>
        </div>
    </div>

    <!-- Statistiques -->
    <h2 class="mb-4">📊 Statistiques</h2>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card border shadow-sm text-center p-4 h-100">
                <h5 class="fw-bold text-primary mb-3">🔤 Mots mêlés</h5>
                <p class="display-5 fw-bold mb-1"><?php echo $temps_moyen_str; ?></p>
                <small class="text-muted">Temps moyen</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border shadow-sm text-center p-4 h-100">
                <h5 class="fw-bold text-primary mb-3">📅 Mot du jour</h5>
                <p class="display-5 fw-bold mb-1"><?php echo $moy_essais; ?></p>
                <small class="text-muted">Score moyen</small>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border shadow-sm text-center p-4 h-100">
                <h5 class="fw-bold text-primary mb-3">🎮 Total</h5>
                <p class="display-5 fw-bold mb-1"><?php echo $total_parties; ?></p>
                <small class="text-muted">Parties jouées</small>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <h2 class="mb-4">📜 Historique des parties</h2>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
                <tr>
                    <th>Date</th>
                    <th>Jeu</th>
                    <th>Score</th>
                    <th>Détail</th>
                    <th>Statut</th>
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
                            <td><?php echo htmlspecialchars($partie['statut']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Aucune partie jouée pour le moment.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
        <nav aria-label="Pagination historique">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?id=<?php echo $user_id; ?>&page=<?php echo $page - 1; ?>">Précédent</a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                        <a class="page-link" href="?id=<?php echo $user_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $total_pages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?id=<?php echo $user_id; ?>&page=<?php echo $page + 1; ?>">Suivant</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>

</div>

<!-- Modal Modifier Profil -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form method="POST" enctype="multipart/form-data" class="modal-content">
            <input type="hidden" name="update_profile" value="1">
            
            <div class="modal-header">
                <h5 class="modal-title">Modifier le profil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold">Nom d'utilisateur</label>
                    <input type="text" name="pseudo" class="form-control" 
                           value="<?php echo htmlspecialchars($user['pseudo']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Bio</label>
                    <textarea name="bio" class="form-control" rows="4" 
                              placeholder="Parlez-nous de vous..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Avatar</label>
                    <input type="file" name="avatar" class="form-control" accept="image/*">
                    <small class="text-muted">Formats acceptés : JPG, PNG, GIF, WebP</small>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-dark">Enregistrer</button>
            </div>
        </form>
    </div>
</div>

<!-- FOOTER -->
<footer class="mt-5 py-4 bg-light border-top text-center text-secondary">
    <div class="container">
        <p class="mb-1">© 2025 JeuxNova — Tous droits réservés</p>
        <small>Contact : support@jeuxnova.com</small>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
