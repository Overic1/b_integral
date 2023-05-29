-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : lun. 29 mai 2023 à 23:55
-- Version du serveur : 10.4.22-MariaDB
-- Version de PHP : 8.1.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bintegral`
--

-- --------------------------------------------------------

--
-- Structure de la table `doctrine_migration_versions`
--

CREATE TABLE `doctrine_migration_versions` (
  `version` varchar(191) COLLATE utf8_unicode_ci NOT NULL,
  `executed_at` datetime DEFAULT NULL,
  `execution_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Déchargement des données de la table `doctrine_migration_versions`
--

INSERT INTO `doctrine_migration_versions` (`version`, `executed_at`, `execution_time`) VALUES
('DoctrineMigrations\\Version20230522152235', '2023-05-22 18:22:48', 8012),
('DoctrineMigrations\\Version20230522174436', '2023-05-22 19:46:20', 3678),
('DoctrineMigrations\\Version20230522174927', '2023-05-22 19:49:33', 153),
('DoctrineMigrations\\Version20230522193131', '2023-05-22 21:32:17', 2357),
('DoctrineMigrations\\Version20230524001858', '2023-05-24 02:19:53', 540),
('DoctrineMigrations\\Version20230524152003', '2023-05-24 17:20:14', 4491),
('DoctrineMigrations\\Version20230524172256', '2023-05-24 19:24:48', 2420),
('DoctrineMigrations\\Version20230526140318', '2023-05-26 16:03:39', 1587),
('DoctrineMigrations\\Version20230526152836', '2023-05-26 17:29:30', 893),
('DoctrineMigrations\\Version20230526155237', '2023-05-26 17:53:40', 2992),
('DoctrineMigrations\\Version20230526172454', '2023-05-26 19:25:03', 2994),
('DoctrineMigrations\\Version20230529131949', '2023-05-29 15:24:44', 1515);

-- --------------------------------------------------------

--
-- Structure de la table `entreprise`
--

CREATE TABLE `entreprise` (
  `id` int(11) NOT NULL,
  `nom` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(180) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `base_url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `api_key` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `num_tel` int(11) DEFAULT NULL,
  `num_ifu` int(11) DEFAULT NULL,
  `num_nim` int(11) DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `installation_id` int(11) DEFAULT NULL,
  `nom_de_la_base` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `pass_base` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `roles` longtext COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '(DC2Type:json)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `entreprise`
--

INSERT INTO `entreprise` (`id`, `nom`, `email`, `password`, `base_url`, `api_key`, `num_tel`, `num_ifu`, `num_nim`, `logo`, `installation_id`, `nom_de_la_base`, `user`, `pass_base`, `roles`) VALUES
(52, 'LAUREX STORE', 'laurexstore@myne2.online', '$2y$10$wAJl6M5UePBBzTaRFzNK9uuQIJq2u.sqjWRz5ezzOqP.gBz81GD6C', 'https://erp.myn2a.online/laurexstore/htdocs/api/', '4NURInpqF2Rl47v1tcyAqQcN2S695GfL98kWhU5X3faWS5dVOz0y4AjPz22k25QtN4', NULL, NULL, NULL, NULL, NULL, 'erplaurex store', 'erp_laurex store', 'S24l#4ZNUMzR7!TU6bct3dH9', '[\"ROLE_USER\"]');

-- --------------------------------------------------------

--
-- Structure de la table `installation`
--

CREATE TABLE `installation` (
  `id` int(11) NOT NULL,
  `nom` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `serveur` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `domaine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `sous_domaine` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `dossier` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `installation`
--

INSERT INTO `installation` (`id`, `nom`, `serveur`, `domaine`, `sous_domaine`, `dossier`) VALUES
(2, 'srger', '68547', 'rgezfqdx', '54', NULL),
(3, 'NET2ALL', '109.228.39.111', 'net2all.online', 'erp', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `messenger_messages`
--

CREATE TABLE `messenger_messages` (
  `id` bigint(20) NOT NULL,
  `body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `headers` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue_name` varchar(190) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL,
  `available_at` datetime NOT NULL,
  `delivered_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `doctrine_migration_versions`
--
ALTER TABLE `doctrine_migration_versions`
  ADD PRIMARY KEY (`version`);

--
-- Index pour la table `entreprise`
--
ALTER TABLE `entreprise`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `UNIQ_D19FA60E7927C74` (`email`),
  ADD KEY `IDX_D19FA60167B88B4` (`installation_id`);

--
-- Index pour la table `installation`
--
ALTER TABLE `installation`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `IDX_75EA56E0FB7336F0` (`queue_name`),
  ADD KEY `IDX_75EA56E0E3BD61CE` (`available_at`),
  ADD KEY `IDX_75EA56E016BA31DB` (`delivered_at`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `entreprise`
--
ALTER TABLE `entreprise`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT pour la table `installation`
--
ALTER TABLE `installation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `messenger_messages`
--
ALTER TABLE `messenger_messages`
  MODIFY `id` bigint(20) NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `entreprise`
--
ALTER TABLE `entreprise`
  ADD CONSTRAINT `FK_D19FA60167B88B4` FOREIGN KEY (`installation_id`) REFERENCES `installation` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
