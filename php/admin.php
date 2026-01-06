<?php
session_start();

// ---------- CONFIG DB ----------
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "bdd_nova";
try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
}
catch (PDOException $e) {
    die("Erreur de connexion DB : " . $e->getMessage());
}
$messages = [];

// ---------- TRAITEMENT DES ACTIONS ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Modifier rôle utilisateur ---
    if ($_POST['action'] === 'update_role') {
        $id = (int) ($_POST['id_utilisateur'] ?? 0);
        $role = trim($_POST['role'] ?? '');
        if ($id > 0 && $role !== '') {
            $stmt = $conn->prepare("UPDATE utilisateur SET role = ? WHERE id_utilisateur = ?");
            $stmt->execute([$role, $id]);
            $messages[] = "Rôle mis à jour.";
        }
    }
    // --- Supprimer utilisateur ---
    if ($_POST['action'] === 'delete_user') {
        $id = (int) ($_POST['id_utilisateur']);

        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id_utilisateur = :id");
            $stmt->execute([':id' => $id]);
            $messages[] = "Utilisateur supprimé.";
        }
    }
    // --- Ajouter grille ---
    if ($_POST['action'] === 'add_grille') {
        $titre       = trim($_POST['titre']);
        $difficulte  = trim($_POST['difficulte']);
        $largeur     = (int) $_POST['largeur'];
        $hauteur     = (int) $_POST['hauteur'];
        $id_utilisateur = (int) ($_POST['id_utilisateur'] ?? 1);

        if ($titre !== '' && $largeur > 0 && $hauteur > 0) {
            $stmt = $conn->prepare("
                INSERT INTO grille (titre, difficulte, largeur, hauteur, date_creation, id_utilisateur)
                VALUES (:titre, :difficulte, :largeur, :hauteur, NOW(), :idu)
            ");
            $stmt->execute([
                ':titre'      => $titre,
                ':difficulte' => $difficulte,
                ':largeur'    => $largeur,
                ':hauteur'    => $hauteur,
                ':idu'        => $id_utilisateur
            ]);
            $messages[] = "Grille ajoutée.";
        }
    }

    // --- Modifier grille ---
    if ($_POST['action'] === 'edit_grille') {
        $id         = (int) $_POST['id_grille'];
        $titre      = trim($_POST['titre']);
        $difficulte = trim($_POST['difficulte']);
        $largeur    = (int) $_POST['largeur'];
        $hauteur    = (int) $_POST['hauteur'];

        if ($id > 0 && $titre !== '' && $largeur > 0 && $hauteur > 0) {
            $stmt = $conn->prepare("
                UPDATE grille
                SET titre = :titre, difficulte = :difficulte, largeur = :largeur, hauteur = :hauteur
                WHERE id_grille = :id
            ");
            $stmt->execute([
                ':titre'      => $titre,
                ':difficulte' => $difficulte,
                ':largeur'    => $largeur,
                ':hauteur'    => $hauteur,
                ':id'         => $id
            ]);
            $messages[] = "Grille mise à jour.";
        }
    }

    // --- Supprimer grille ---
    if ($_POST['action'] === 'delete_grille') {
        $id = (int) $_POST['id_grille'];

        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM grille WHERE id_grille = :id");
            $stmt->execute([':id' => $id]);
            $messages[] = "Grille supprimée.";
        }
    }
}

// ---------- CHARGER DONNÉES ----------
$users = $conn->query("
    SELECT id_utilisateur, email, pseudo, role, date_creation
    FROM utilisateur
    ORDER BY date_creation DESC
")->fetchAll(PDO::FETCH_ASSOC);

$grilles = $conn->query("
    SELECT g.id_grille, g.titre, g.difficulte, g.largeur, g.hauteur, g.date_creation,
           u.pseudo AS createur
    FROM grille g
    LEFT JOIN utilisateur u ON g.id_utilisateur = u.id_utilisateur
    ORDER BY g.date_creation DESC
")->fetchAll(PDO::FETCH_ASSOC);

// Fonction sécurité HTML
function h($s){ return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

?>
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="utf-8">
        <title>Admin - Gestion</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="stylesheet" href="../css/styles.css">
        <style>
            body { 
                /* background:#f3f6ff;  */
                padding-bottom:40px; 
            }

            .topbar { 
                background: #fff; 
                box-shadow: 0 2px 6px rgba(0,0,0,0.06); 
                padding:12px 20px; 
                margin-bottom:20px; 
            }

            .panel { 
                background:#fff; 
                border-radius: 12px; 
                padding: 18px; 
                border: 1px solid #e6ecff; 
            }
            
            .small-muted { 
                color: #6c757d; 
                font-size: 0.9rem; 
            }
            
            .table-wrap { 
                max-height: 380px; 
                overflow: auto; 
            }
        </style>
    </head>
    <body>
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom position-relative">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="/projet-web-eseo/index.php">
                    <img src="../assets/logo.png" alt="Logo" height="40" class="me-2">
                </a>

                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navMenu">
                    <ul class="navbar-nav navbar-nav-centered">
                        <li class="nav-item"><a class="nav-link fw-semibold" href="/projet-web-eseo/php/mots.php">Jeux</a></li>
                        <li class="nav-item"><a class="nav-link fw-semibold" href="/projet-web-eseo/php/leaderboard.php">Leaderboard</a></li>
                    </ul>

                    <div class="d-flex ms-auto nav-auth">
                        <?php if (isset($_SESSION["id"])): ?>
                            <a class="btn btn-outline-dark me-2" href="/projet-web-eseo/php/admin.php">Admin</a>
                            <a class="btn btn-outline-dark me-2" href="/projet-web-eseo/php/profil.php">Mon Profil</a>
                            <a class="btn btn-dark" href="/projet-web-eseo/php/deconexion.php">Déconnexion</a>
                        <?php else: ?>
                            <a class="btn btn-outline-dark me-2" href="/projet-web-eseo/php/account.php?mode=login">Connexion</a>
                            <a class="btn btn-dark" href="/projet-web-eseo/php/account.php?mode=register">S'inscrire</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </nav>
        <div class="container mt-4">
            <div class="topbar d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Admin — Gestion Utilisateurs, Grilles & Mots</h4>
                <div>
                    <a href="/projet-web-eseo/php/gestion_du_mots_du_jour.php"><button class="btn btn-primary btn-sm">+ Ajouter mot</button></a>
                    <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddGrille">+ Ajouter grille</button>
                </div>
            </div>

            <!-- messages -->
            <?php if (!empty($messages)): ?>
                <div class="mb-3">
                    <?php foreach ($messages as $m): ?>
                        <div class="alert alert-info py-2"><?= h($m) ?></div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <div class="row g-3">
                <!-- UTILISATEURS -->
                <div class="col-12 col-lg-6">
                    <div class="panel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Utilisateurs</h5>
                            <span class="small-muted"><?= count($users) ?> total</span>
                        </div>

                        <div class="table-wrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Pseudo</th>
                                        <th>Email</th>
                                        <th>Rôle</th>
                                        <th>Créé</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                <?php if (!empty($users)): ?>
                                <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><?= h($u['pseudo']) ?></td>
                                    <td><?= h($u['email']) ?></td>
                                    <td><?= h($u['role']) ?></td>
                                    <td><?= h($u['date_creation']) ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-primary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditRole"
                                                data-id="<?= $u['id_utilisateur'] ?>"
                                                data-role="<?= h($u['role']) ?>"
                                                data-pseudo="<?= h($u['pseudo']) ?>">
                                            Modifier rôle
                                        </button>

                                        <form method="post" style="display:inline-block" onsubmit="return confirm('Supprimer cet utilisateur ?');">
                                            <input type="hidden" name="action" value="delete_user">
                                            <input type="hidden" name="id_utilisateur" value="<?= $u['id_utilisateur'] ?>">
                                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5">Aucun utilisateur</td></tr>
                                <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- GRILLES -->
                <div class="col-12 col-lg-6">
                    <div class="panel">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="mb-0">Grilles (Mots Melees)</h5>
                            <span class="small-muted"><?= count($grilles) ?> total</span>
                        </div>

                        <div class="table-wrap">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Difficulté</th>
                                        <th>Taille</th>
                                        <th>Créateur</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>

                                <?php if (!empty($grilles)): ?>
                                <?php foreach ($grilles as $g): ?>
                                <tr>
                                    <td><?= h($g['titre']) ?></td>
                                    <td><?= h($g['difficulte']) ?></td>
                                    <td><?= h($g['largeur']) ?> × <?= h($g['hauteur']) ?></td>
                                    <td><?= h($g['createur'] ?? '—') ?></td>
                                    <td class="text-end">
                                        <button class="btn btn-sm btn-outline-secondary"
                                                data-bs-toggle="modal"
                                                data-bs-target="#modalEditGrille"
                                                data-id="<?= $g['id_grille'] ?>"
                                                data-titre="<?= h($g['titre']) ?>"
                                                data-difficulte="<?= h($g['difficulte']) ?>"
                                                data-largeur="<?= h($g['largeur']) ?>"
                                                data-hauteur="<?= h($g['hauteur']) ?>">
                                            Modifier
                                        </button>

                                        <form method="post" style="display:inline-block" onsubmit="return confirm('Supprimer cette grille ?');">
                                            <input type="hidden" name="action" value="delete_grille">
                                            <input type="hidden" name="id_grille" value="<?= $g['id_grille'] ?>">
                                            <button class="btn btn-sm btn-outline-danger">Supprimer</button>
                                        </form>

                                        <a href="voir_grille.php?id=<?= $g['id_grille'] ?>" class="btn btn-sm btn-outline-info">
                                            Voir
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php else: ?>
                                    <tr><td colspan="5">Aucune grille</td></tr>
                                <?php endif; ?>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    <!-- MODAL EDIT ROLE -->
        <div class="modal fade" id="modalEditRole" tabindex="-1">
            <div class="modal-dialog">
                <form method="post" class="modal-content">
                    <input type="hidden" name="action" value="update_role">
                    <input type="hidden" name="id_utilisateur" id="er_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier le rôle — <span id="er_pseudo"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <label class="form-label">Rôle</label>
                        <select name="role" id="er_role" class="form-select" required>
                            <option value="user">user</option>
                            <option value="moderator">moderator</option>
                            <option value="admin">admin</option>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL AJOUT GRILLE -->
        <div class="modal fade" id="modalAddGrille" tabindex="-1">
            <div class="modal-dialog">
                <form method="post" class="modal-content">
                    <input type="hidden" name="action" value="add_grille">

                    <div class="modal-header">
                        <h5 class="modal-title">Ajouter une grille</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="form-label">Titre</label>
                        <input name="titre" class="form-control" required>

                        <label class="form-label mt-2">Difficulté</label>
                        <select name="difficulte" class="form-control" required>
                            <option value="Facile">Facile</option>
                            <option value="Moyenne">Moyenne</option>
                            <option value="Difficile">Difficile</option>
                        </select>
                        <!-- <input name="difficulte" class="form-control" required> -->

                        <div class="row mt-2">
                            <div class="col">
                                <label class="form-label">Largeur</label>
                                <input type="number" name="largeur" class="form-control" min="1" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Hauteur</label>
                                <input type="number" name="hauteur" class="form-control" min="1" required>
                            </div>
                        </div>

                        <label class="form-label mt-2">ID créateur</label>
                        <input type="number" name="id_utilisateur" class="form-control" placeholder="1">

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button class="btn btn-primary">Ajouter</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL EDIT GRILLE -->
        <div class="modal fade" id="modalEditGrille" tabindex="-1">
            <div class="modal-dialog">
                <form method="post" class="modal-content">
                    <input type="hidden" name="action" value="edit_grille">
                    <input type="hidden" name="id_grille" id="eg_id">

                    <div class="modal-header">
                        <h5 class="modal-title">Modifier la grille</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <label class="form-label">Titre</label>
                        <input name="titre" id="eg_titre" class="form-control" required>

                        <label class="form-label mt-2">Difficulté</label>
                        <select name="difficulte" class="form-control" id="eg_difficulte" required>
                            <option value="Facile">Facile</option>
                            <option value="Moyenne">Moyenne</option>
                            <option value="Difficile">Difficile</option>
                        </select>

                        <div class="row mt-2">
                            <div class="col">
                                <label class="form-label">Largeur</label>
                                <input type="number" name="largeur" id="eg_largeur" class="form-control" min="1" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Hauteur</label>
                                <input type="number" name="hauteur" id="eg_hauteur" class="form-control" min="1" required>
                            </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <button class="btn btn-primary">Enregistrer</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            // Pré-remplir modal rôle
            let modalEditRole = document.getElementById('modalEditRole');
            modalEditRole.addEventListener('show.bs.modal', function (event) {
                let btn = event.relatedTarget;
                document.getElementById('er_id').value = btn.getAttribute('data-id');
                document.getElementById('er_role').value = btn.getAttribute('data-role');
                document.getElementById('er_pseudo').innerText = btn.getAttribute('data-pseudo');
            });

            // Pré-remplir modal grille
            let modalEditGrille = document.getElementById('modalEditGrille');
            modalEditGrille.addEventListener('show.bs.modal', function (event) {
                let btn = event.relatedTarget;
                document.getElementById('eg_id').value = btn.getAttribute('data-id');
                document.getElementById('eg_titre').value = btn.getAttribute('data-titre');
                document.getElementById('eg_difficulte').value = btn.getAttribute('data-difficulte');
                document.getElementById('eg_largeur').value = btn.getAttribute('data-largeur');
                document.getElementById('eg_hauteur').value = btn.getAttribute('data-hauteur');
            });
        </script>
    </body>
</html>