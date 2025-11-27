<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "jeu_mots";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion DB: " . $e->getMessage());
}

$messages = [];

// ----------------------
// TRAITEMENT DES FORMULAIRES
// ----------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // 🔹 Modifier rôle utilisateur
    if ($_POST['action'] === 'update_role') {

        $id = (int)($_POST['id_utilisateur'] ?? 0);
        $role = $_POST['role'] ?? "";

        if ($id > 0 && $role !== "") {
            $stmt = $conn->prepare("UPDATE utilisateur SET role=? WHERE id_utilisateur=?");
            $stmt->execute([$role, $id]);
            $messages[] = "Rôle mis à jour.";
        }
    }

    // 🔹 Supprimer utilisateur
    if ($_POST['action'] === 'delete_user') {

        $id = (int)($_POST['id_utilisateur'] ?? 0);

        if ($id > 0) {
            $stmt = $conn->prepare("DELETE FROM utilisateur WHERE id_utilisateur=?");
            $stmt->execute([$id]);
            $messages[] = "Utilisateur supprimé.";
        }
    }
    if ($_POST['action']==='mots-jeu'){


    }

}

// 🔹 Récupérer tous les utilisateurs
$users = $conn->query("SELECT id_utilisateur, email, pseudo, role, date_creation
                       FROM utilisateur
                       ORDER BY date_creation DESC")
              ->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

<div class="container py-4">

    <h4>Admin — Gestion Utilisateurs</h4>

    <div class="panel mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5>Utilisateurs</h5>
            <span class="text-muted"><?= count($users) ?> total</span>
        </div>

        <div class="table-wrap">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Pseudo</th>
                    <th>Email</th>
                    <th>Rôle</th>
                    <th>Créé le</th>
                    <th class="text-end">Actions</th>
                </tr>
                </thead>

                <tbody>
                <?php if (!empty($users)): ?>
                    <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['pseudo']) ?></td>
                            <td><?= htmlspecialchars($u['email']) ?></td>
                            <td><?= htmlspecialchars($u['role']) ?></td>
                            <td><?= htmlspecialchars($u['date_creation']) ?></td>

                            <td class="text-end">
                                <!-- BTN MODIFIER ROLE -->
                                <button class="btn btn-sm btn-outline-primary"
                                        data-bs-toggle="modal"
                                        data-bs-target="#modalEditRole"
                                        data-id="<?= $u['id_utilisateur'] ?>"
                                        data-role="<?= htmlspecialchars($u['role']) ?>"
                                        data-pseudo="<?= htmlspecialchars($u['pseudo']) ?>">
                                    Modifier rôle
                                </button>

                                <!-- BTN SUPPRIMER -->
                                <form method="POST" style="display:inline-block"
                                      onsubmit="return confirm('Supprimer cet utilisateur ?');">

                                    <input type="hidden" name="action" value="delete_user">
                                    <input type="hidden" name="id_utilisateur"
                                           value="<?= $u['id_utilisateur'] ?>">

                                    <button class="btn btn-sm btn-outline-danger">
                                        Supprimer
                                    </button>

                                </form>
                            </td>
                        </tr>

                    <?php endforeach; ?>
                <?php endif; ?>
                </tbody>

            </table>
        </div>

    </div>
</div>

<!-- ---------------------- -->
<!-- MODAL EDIT ROLE -->
<!-- ---------------------- -->

<div class="modal fade" id="modalEditRole" tabindex="-1" aria-hidden="true">
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
                <select name="role" id="er_role" class="form-select">
                    <option value="user">user</option>
                    <option value="moderator">moderator</option>
                    <option value="admin">admin</option>
                </select>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button class="btn btn-primary" type="submit">Enregistrer</button>
            </div>

        </form>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Remplissage automatique du modal
var modalEditRole = document.getElementById('modalEditRole');
modalEditRole.addEventListener('show.bs.modal', function (event) {

    var btn = event.relatedTarget;

    document.getElementById('er_id').value = btn.getAttribute('data-id');
    document.getElementById('er_role').value = btn.getAttribute('data-role');
    document.getElementById('er_pseudo').innerText = btn.getAttribute('data-pseudo');

});
</script>

</body>
</html>