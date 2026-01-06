-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : jeu. 20 nov. 2025 à 14:47
-- Version du serveur : 9.1.0
-- Version de PHP : 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bdd_nova`
--

-- --------------------------------------------------------

--
-- Structure de la table `grille`
--

DROP TABLE IF EXISTS `grille`;
CREATE TABLE IF NOT EXISTS `grille` (
  `id_grille` int NOT NULL AUTO_INCREMENT,
  `titre` varchar(255) DEFAULT NULL,
  `difficulte` varchar(50) DEFAULT NULL,
  `largeur` int NOT NULL,
  `hauteur` int NOT NULL,
  `date_creation` datetime NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_grille`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `indice`
--

DROP TABLE IF EXISTS `indice`;
CREATE TABLE IF NOT EXISTS `indice` (
  `id_indice` int NOT NULL AUTO_INCREMENT,
  `numero` int NOT NULL,
  `direction` varchar(10) NOT NULL,
  `ligne` int NOT NULL,
  `colonne` int NOT NULL,
  `longueur` int NOT NULL,
  `texte_indice` text NOT NULL,
  `solution` varchar(255) NOT NULL,
  `id_grille` int NOT NULL,
  PRIMARY KEY (`id_indice`),
  KEY `id_grille` (`id_grille`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `leaderboard`
--

DROP TABLE IF EXISTS `leaderboard`;
CREATE TABLE IF NOT EXISTS `leaderboard` (
  `id_leaderboard` int NOT NULL AUTO_INCREMENT,
  `score` int NOT NULL,
  `date_entree` datetime NOT NULL,
  `id_partie` int NOT NULL,
  PRIMARY KEY (`id_leaderboard`),
  UNIQUE KEY `id_partie` (`id_partie`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `motdujour`
--

DROP TABLE IF EXISTS `motdujour`;
CREATE TABLE IF NOT EXISTS `motdujour` (
  `id_mot` int NOT NULL AUTO_INCREMENT,
  `mot` varchar(255) NOT NULL,
  `definition` text NOT NULL,
  `date` date NOT NULL,
  `id_utilisateur` int NOT NULL,
  PRIMARY KEY (`id_mot`),
  UNIQUE KEY `date` (`date`),
  KEY `id_utilisateur` (`id_utilisateur`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `partie`
--

DROP TABLE IF EXISTS `partie`;
CREATE TABLE IF NOT EXISTS `partie` (
  `id_partie` int NOT NULL AUTO_INCREMENT,
  `date_debut` datetime NOT NULL,
  `date_fin` datetime DEFAULT NULL,
  `score` int DEFAULT NULL,
  `statut` varchar(50) DEFAULT NULL,
  `id_utilisateur` int NOT NULL,
  `id_grille` int DEFAULT NULL,
  `id_mot` int DEFAULT NULL,
  PRIMARY KEY (`id_partie`),
  KEY `id_utilisateur` (`id_utilisateur`),
  KEY `id_grille` (`id_grille`),
  KEY `id_mot` (`id_mot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `progression`
--

DROP TABLE IF EXISTS `progression`;
CREATE TABLE IF NOT EXISTS `progression` (
  `id_progression` int NOT NULL AUTO_INCREMENT,
  `etat` text NOT NULL,
  `date_mise_a_jour` datetime NOT NULL,
  `id_partie` int NOT NULL,
  `id_grille` int NOT NULL,
  PRIMARY KEY (`id_progression`),
  UNIQUE KEY `id_partie` (`id_partie`),
  KEY `id_grille` (`id_grille`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

-- DROP TABLE IF EXISTS `motsvalides`;
-- CREATE TABLE IF NOT EXISTS `motsvalides` (
--   `id_motvalide` int NOT NULL AUTO_INCREMENT,
--   `motvalide` text NOT NULL,
--   PRIMARY KEY (`id_motvalide`)
-- )




--
-- Structure de la table `utilisateur`
--

DROP TABLE IF EXISTS `utilisateur`;
CREATE TABLE IF NOT EXISTS `utilisateur` (
  `id_utilisateur` int NOT NULL AUTO_INCREMENT,
  `email` varchar(200) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `pseudo` varchar(100) NOT NULL,
  `role` varchar(50) NOT NULL,
  `date_creation` datetime NOT NULL,
  `bio` TEXT NULL,
  `avatar` VARCHAR(255) NULL,
  PRIMARY KEY (`id_utilisateur`),
  UNIQUE KEY `email` (`email`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
