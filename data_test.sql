-- ============================================
-- Script de données de test pour projet Nova
-- ============================================

-- Note: Si les colonnes bio et avatar existent déjà, ces commandes échoueront silencieusement
-- C'est normal si vous avez déjà importé bdd_nova.sql mis à jour

-- 1. Ajouter les colonnes manquantes (si nécessaire)
-- Ignorez les erreurs "Duplicate column name" si elles apparaissent
ALTER TABLE `utilisateur` ADD COLUMN `bio` TEXT NULL;
ALTER TABLE `utilisateur` ADD COLUMN `avatar` VARCHAR(255) NULL;

-- 2. Créer des utilisateurs de test
INSERT IGNORE INTO `utilisateur` (`id_utilisateur`, `email`, `mot_de_passe`, `pseudo`, `role`, `date_creation`, `bio`) VALUES 
(1, 'test@nova.com', 'password123', 'NovaExplorer', 'joueur', NOW(), 'Passionné de mots et de casse-têtes !'),
(2, 'player2@nova.com', 'password123', 'WordMaster', 'joueur', NOW(), 'Champion des mots mêlés'),
(3, 'player3@nova.com', 'password123', 'PuzzleKing', 'joueur', NOW(), 'Roi des puzzles'),
(4, 'player4@nova.com', 'password123', 'QuickTyper', 'joueur', NOW(), 'Rapide comme l''éclair'),
(5, 'player5@nova.com', 'password123', 'BrainStorm', 'joueur', NOW(), 'Réflexion intense');

-- 3. Insérer des parties pour le Leaderboard (Mot Mêlé - score = temps en secondes)
INSERT INTO `partie` (`date_debut`, `date_fin`, `score`, `statut`, `id_utilisateur`, `id_grille`, `id_mot`) VALUES 
-- Parties d'aujourd'hui
(NOW(), NOW(), 45, 'termine', 1, 1, NULL),   -- NovaExplorer: 0:45
(NOW(), NOW(), 55, 'termine', 2, 1, NULL),   -- WordMaster: 0:55
(NOW(), NOW(), 70, 'termine', 3, 1, NULL),   -- PuzzleKing: 1:10
(NOW(), NOW(), 85, 'termine', 4, 1, NULL),   -- QuickTyper: 1:25
(NOW(), NOW(), 120, 'termine', 5, 1, NULL),  -- BrainStorm: 2:00

-- Parties plus anciennes (pour le filtre semaine/mois)
(DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 50, 'termine', 2, 1, NULL),
(DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 65, 'termine', 1, 1, NULL),
(DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 40, 'termine', 3, 1, NULL);

-- 4. Insérer des parties Mot du Jour (score = nombre d'essais)
INSERT INTO `partie` (`date_debut`, `date_fin`, `score`, `statut`, `id_utilisateur`, `id_grille`, `id_mot`) VALUES 
-- Parties d'aujourd'hui
(NOW(), NOW(), 3, 'termine', 1, NULL, 1),   -- NovaExplorer: 3 essais
(NOW(), NOW(), 4, 'termine', 2, NULL, 1),   -- WordMaster: 4 essais
(NOW(), NOW(), 2, 'termine', 3, NULL, 1),   -- PuzzleKing: 2 essais (meilleur!)
(NOW(), NOW(), 5, 'termine', 4, NULL, 1),   -- QuickTyper: 5 essais
(NOW(), NOW(), 6, 'termine', 5, NULL, 1),   -- BrainStorm: 6 essais

-- Parties plus anciennes
(DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 4, 'termine', 1, NULL, 1),
(DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 3, 'termine', 2, NULL, 1);

-- ============================================
-- Après avoir exécuté ce script, testez :
-- - Profil : http://localhost:8090/projet-web-ESEO/pages/profil.php?id=1
-- - Leaderboard : http://localhost:8090/projet-web-ESEO/pages/leaderboard.php
-- ============================================
