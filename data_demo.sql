-- ============================================
-- Script de données de démonstration complètes
-- Projet Nova - Jeux de mots
-- ============================================

-- Suppression des anciennes données (optionnel - à commenter si vous voulez conserver)
-- DELETE FROM partie;
-- DELETE FROM utilisateur;
-- DELETE FROM motdujour;
-- DELETE FROM grille;

-- ============================================
-- 1. UTILISATEURS (15 joueurs)
-- ============================================
INSERT INTO `utilisateur` (`id_utilisateur`, `email`, `mot_de_passe`, `pseudo`, `role`, `date_creation`, `bio`, `avatar`) VALUES 
(1, 'hugo@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'HugoBoss', 'admin', '2025-09-01 10:00:00', 'Créateur du projet Nova. Passionné de mots et de logique.', NULL),
(2, 'marie@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'MarieLexique', 'joueur', '2025-09-15 14:30:00', 'Amoureuse des mots depuis toujours 📚', NULL),
(3, 'pierre@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'PierreLaPierre', 'joueur', '2025-10-02 09:00:00', 'Speed runner de mots mêlés ⚡', NULL),
(4, 'sophie@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'SophieWords', 'joueur', '2025-10-10 16:45:00', 'Championne régionale de Scrabble 🏆', NULL),
(5, 'lucas@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'LucasGamer', 'joueur', '2025-10-20 11:20:00', 'Je joue pour le fun !', NULL),
(6, 'emma@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'EmmaLetters', 'joueur', '2025-11-01 08:00:00', 'Professeur de français le jour, joueuse la nuit.', NULL),
(7, 'thomas@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'ThomasThink', 'joueur', '2025-11-05 19:30:00', 'Réflexion et stratégie.', NULL),
(8, 'julie@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'JulieJoueuse', 'joueur', '2025-11-10 12:00:00', 'Fan de puzzles et énigmes', NULL),
(9, 'antoine@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'AntoineAce', 'joueur', '2025-11-15 17:00:00', 'Top 10 mondial sur Wordle 🌍', NULL),
(10, 'camille@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'CamilleCalme', 'joueur', '2025-11-20 10:10:00', 'Je prends mon temps, mais je gagne.', NULL),
(11, 'maxime@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'MaxSpeed', 'joueur', '2025-12-01 15:00:00', 'Le plus rapide de l''Ouest 🤠', NULL),
(12, 'lea@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'LeaLecture', 'joueur', '2025-12-05 09:45:00', 'Dévoreuse de livres et de grilles.', NULL),
(13, 'nathan@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'NathanNeuro', 'joueur', '2025-12-10 14:00:00', 'Étudiant en neurosciences, j''entraîne mon cerveau !', NULL),
(14, 'chloe@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'ChloéChampion', 'joueur', '2025-12-15 11:30:00', '3x championne du mois 🥇🥇🥇', NULL),
(15, 'alex@nova.com', '$2y$10$abcdefghijklmnopqrstuv', 'AlexAlpha', 'joueur', '2025-12-20 16:00:00', 'Nouveau mais motivé !', NULL)
ON DUPLICATE KEY UPDATE pseudo = VALUES(pseudo);

-- ============================================
-- 2. MOTS DU JOUR (30 derniers jours)
-- ============================================
INSERT INTO `motdujour` (`id_mot`, `mot`, `definition`, `date`, `id_utilisateur`) VALUES 
(1, 'CRANE', 'Boîte osseuse contenant le cerveau', CURDATE(), 1),
(2, 'PIANO', 'Instrument de musique à clavier', DATE_SUB(CURDATE(), INTERVAL 1 DAY), 1),
(3, 'TIGRE', 'Grand félin rayé d''Asie', DATE_SUB(CURDATE(), INTERVAL 2 DAY), 1),
(4, 'PLAGE', 'Étendue de sable au bord de la mer', DATE_SUB(CURDATE(), INTERVAL 3 DAY), 1),
(5, 'LIVRE', 'Ouvrage imprimé relié', DATE_SUB(CURDATE(), INTERVAL 4 DAY), 1),
(6, 'NUAGE', 'Masse de vapeur d''eau dans le ciel', DATE_SUB(CURDATE(), INTERVAL 5 DAY), 1),
(7, 'CHAIR', 'Tissu musculaire du corps', DATE_SUB(CURDATE(), INTERVAL 6 DAY), 1),
(8, 'FLEUR', 'Organe reproducteur des plantes', DATE_SUB(CURDATE(), INTERVAL 7 DAY), 1),
(9, 'MONDE', 'Ensemble de tout ce qui existe', DATE_SUB(CURDATE(), INTERVAL 8 DAY), 1),
(10, 'PORTE', 'Ouverture permettant de passer', DATE_SUB(CURDATE(), INTERVAL 9 DAY), 1),
(11, 'ARBRE', 'Grande plante à tronc ligneux', DATE_SUB(CURDATE(), INTERVAL 10 DAY), 1),
(12, 'TRAIN', 'Moyen de transport ferroviaire', DATE_SUB(CURDATE(), INTERVAL 11 DAY), 1),
(13, 'COEUR', 'Organe central de la circulation', DATE_SUB(CURDATE(), INTERVAL 12 DAY), 1),
(14, 'TERRE', 'Planète sur laquelle nous vivons', DATE_SUB(CURDATE(), INTERVAL 13 DAY), 1),
(15, 'CHIEN', 'Animal domestique fidèle', DATE_SUB(CURDATE(), INTERVAL 14 DAY), 1)
ON DUPLICATE KEY UPDATE mot = VALUES(mot);

-- ============================================
-- 3. GRILLES (Mots mêlés)
-- ============================================
INSERT INTO `grille` (`id_grille`, `titre`, `difficulte`, `largeur`, `hauteur`, `date_creation`, `id_utilisateur`) VALUES 
(1, 'Grille Facile 1', 'facile', 10, 10, '2025-12-01 10:00:00', 1),
(2, 'Grille Facile 2', 'facile', 10, 10, '2025-12-05 10:00:00', 1),
(3, 'Grille Moyenne 1', 'moyen', 12, 12, '2025-12-10 10:00:00', 1),
(4, 'Grille Difficile 1', 'difficile', 15, 15, '2025-12-15 10:00:00', 1),
(5, 'Grille Expert', 'expert', 20, 20, '2025-12-20 10:00:00', 1)
ON DUPLICATE KEY UPDATE titre = VALUES(titre);

-- ============================================
-- 4. PARTIES - MOTS MÊLÉS (score = temps en secondes)
-- ============================================

-- Aujourd'hui
INSERT INTO `partie` (`date_debut`, `date_fin`, `score`, `statut`, `id_utilisateur`, `id_grille`, `id_mot`) VALUES 
(NOW(), NOW(), 32, 'termine', 14, 1, NULL),  -- ChloéChampion: 0:32 🥇
(NOW(), NOW(), 38, 'termine', 11, 1, NULL),  -- MaxSpeed: 0:38 🥈
(NOW(), NOW(), 45, 'termine', 4, 1, NULL),   -- SophieWords: 0:45 🥉
(NOW(), NOW(), 52, 'termine', 9, 1, NULL),   -- AntoineAce: 0:52
(NOW(), NOW(), 58, 'termine', 3, 1, NULL),   -- PierreLaPierre: 0:58
(NOW(), NOW(), 65, 'termine', 1, 1, NULL),   -- HugoBoss: 1:05
(NOW(), NOW(), 72, 'termine', 2, 1, NULL),   -- MarieLexique: 1:12
(NOW(), NOW(), 85, 'termine', 6, 1, NULL),   -- EmmaLetters: 1:25
(NOW(), NOW(), 95, 'termine', 7, 1, NULL),   -- ThomasThink: 1:35
(NOW(), NOW(), 110, 'termine', 8, 1, NULL),  -- JulieJoueuse: 1:50
(NOW(), NOW(), 125, 'termine', 10, 1, NULL), -- CamilleCalme: 2:05
(NOW(), NOW(), 140, 'termine', 12, 1, NULL), -- LeaLecture: 2:20
(NOW(), NOW(), 180, 'termine', 15, 1, NULL); -- AlexAlpha: 3:00

-- Cette semaine (2-6 jours)
INSERT INTO `partie` (`date_debut`, `date_fin`, `score`, `statut`, `id_utilisateur`, `id_grille`, `id_mot`) VALUES 
(DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 28, 'termine', 14, 2, NULL),
(DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 35, 'termine', 11, 2, NULL),
(DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 42, 'termine', 9, 2, NULL),
(DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 48, 'termine', 4, 2, NULL),
(DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 55, 'termine', 3, 2, NULL),
(DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 40, 'termine', 1, 2, NULL),
(DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), 62, 'termine', 2, 2, NULL);

-- Ce mois (7-25 jours)
INSERT INTO `partie` (`date_debut`, `date_fin`, `score`, `statut`, `id_utilisateur`, `id_grille`, `id_mot`) VALUES 
(DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), 25, 'termine', 14, 3, NULL),
(DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), 30, 'termine', 11, 3, NULL),
(DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY), 38, 'termine', 9, 3, NULL),
(DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY), 45, 'termine', 4, 3, NULL),
(DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY), 52, 'termine', 6, 3, NULL),
(DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY), 58, 'termine', 7, 3, NULL),
(DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY), 35, 'termine', 13, 3, NULL),
(DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY), 42, 'termine', 12, 3, NULL);

-- ============================================
-- 5. PARTIES - MOT DU JOUR (score = nombre d'essais)
-- ============================================

-- Aujourd'hui
INSERT INTO `partie` (`date_debut`, `date_fin`, `score`, `statut`, `id_utilisateur`, `id_grille`, `id_mot`) VALUES 
(NOW(), NOW(), 2, 'termine', 9, NULL, 1),   -- AntoineAce: 2 essais 🥇
(NOW(), NOW(), 2, 'termine', 14, NULL, 1),  -- ChloéChampion: 2 essais 🥈
(NOW(), NOW(), 3, 'termine', 4, NULL, 1),   -- SophieWords: 3 essais 🥉
(NOW(), NOW(), 3, 'termine', 6, NULL, 1),   -- EmmaLetters: 3 essais
(NOW(), NOW(), 4, 'termine', 1, NULL, 1),   -- HugoBoss: 4 essais
(NOW(), NOW(), 4, 'termine', 2, NULL, 1),   -- MarieLexique: 4 essais
(NOW(), NOW(), 4, 'termine', 3, NULL, 1),   -- PierreLaPierre: 4 essais
(NOW(), NOW(), 5, 'termine', 5, NULL, 1),   -- LucasGamer: 5 essais
(NOW(), NOW(), 5, 'termine', 7, NULL, 1),   -- ThomasThink: 5 essais
(NOW(), NOW(), 5, 'termine', 11, NULL, 1),  -- MaxSpeed: 5 essais
(NOW(), NOW(), 6, 'termine', 8, NULL, 1),   -- JulieJoueuse: 6 essais
(NOW(), NOW(), 6, 'termine', 10, NULL, 1),  -- CamilleCalme: 6 essais
(NOW(), NOW(), 6, 'termine', 15, NULL, 1);  -- AlexAlpha: 6 essais (nouveau joueur)

-- Cette semaine
INSERT INTO `partie` (`date_debut`, `date_fin`, `score`, `statut`, `id_utilisateur`, `id_grille`, `id_mot`) VALUES 
(DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 2, 'termine', 14, NULL, 2),
(DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 3, 'termine', 9, NULL, 2),
(DATE_SUB(NOW(), INTERVAL 1 DAY), DATE_SUB(NOW(), INTERVAL 1 DAY), 3, 'termine', 4, NULL, 2),
(DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 2, 'termine', 9, NULL, 3),
(DATE_SUB(NOW(), INTERVAL 2 DAY), DATE_SUB(NOW(), INTERVAL 2 DAY), 4, 'termine', 1, NULL, 3),
(DATE_SUB(NOW(), INTERVAL 3 DAY), DATE_SUB(NOW(), INTERVAL 3 DAY), 3, 'termine', 6, NULL, 4),
(DATE_SUB(NOW(), INTERVAL 4 DAY), DATE_SUB(NOW(), INTERVAL 4 DAY), 4, 'termine', 2, NULL, 5),
(DATE_SUB(NOW(), INTERVAL 5 DAY), DATE_SUB(NOW(), INTERVAL 5 DAY), 3, 'termine', 11, NULL, 6),
(DATE_SUB(NOW(), INTERVAL 6 DAY), DATE_SUB(NOW(), INTERVAL 6 DAY), 5, 'termine', 7, NULL, 7);

-- Ce mois
INSERT INTO `partie` (`date_debut`, `date_fin`, `score`, `statut`, `id_utilisateur`, `id_grille`, `id_mot`) VALUES 
(DATE_SUB(NOW(), INTERVAL 8 DAY), DATE_SUB(NOW(), INTERVAL 8 DAY), 2, 'termine', 14, NULL, 8),
(DATE_SUB(NOW(), INTERVAL 10 DAY), DATE_SUB(NOW(), INTERVAL 10 DAY), 3, 'termine', 9, NULL, 9),
(DATE_SUB(NOW(), INTERVAL 12 DAY), DATE_SUB(NOW(), INTERVAL 12 DAY), 3, 'termine', 4, NULL, 10),
(DATE_SUB(NOW(), INTERVAL 15 DAY), DATE_SUB(NOW(), INTERVAL 15 DAY), 4, 'termine', 6, NULL, 11),
(DATE_SUB(NOW(), INTERVAL 18 DAY), DATE_SUB(NOW(), INTERVAL 18 DAY), 2, 'termine', 13, NULL, 12),
(DATE_SUB(NOW(), INTERVAL 20 DAY), DATE_SUB(NOW(), INTERVAL 20 DAY), 5, 'termine', 12, NULL, 13),
(DATE_SUB(NOW(), INTERVAL 22 DAY), DATE_SUB(NOW(), INTERVAL 22 DAY), 3, 'termine', 3, NULL, 14),
(DATE_SUB(NOW(), INTERVAL 25 DAY), DATE_SUB(NOW(), INTERVAL 25 DAY), 4, 'termine', 10, NULL, 15);

-- ============================================
-- FIN DU SCRIPT
-- ============================================
-- Pour importer ce fichier :
-- 1. Allez sur phpMyAdmin
-- 2. Sélectionnez la base de données "bdd_nova"
-- 3. Cliquez sur "Importer"
-- 4. Sélectionnez ce fichier et exécutez
--
-- URLs de test après import :
-- Profil : http://localhost/projet-web-ESEO/pages/profil.php?id=1
-- Leaderboard : http://localhost/projet-web-ESEO/pages/leaderboard.php
-- ============================================
