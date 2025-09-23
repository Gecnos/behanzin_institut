-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Hôte : db
-- Généré le : mar. 23 sep. 2025 à 18:10
-- Version du serveur : 8.0.43
-- Version de PHP : 8.2.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `journaux`
--

-- --------------------------------------------------------

--
-- Structure de la table `Articles`
--

CREATE TABLE `Articles` (
  `id_article` int NOT NULL,
  `titre` varchar(255) NOT NULL,
  `titre_en` varchar(255) DEFAULT NULL,
  `resume` text,
  `resume_en` text,
  `fichier_manuscrit` varchar(255) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `statut` enum('en attente','accepté','refusé','publié') NOT NULL,
  `est_en_avant` tinyint(1) NOT NULL DEFAULT '0',
  `date_soumission` datetime NOT NULL,
  `date_publication` datetime DEFAULT NULL,
  `id_auteur` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Articles`
--

INSERT INTO `Articles` (`id_article`, `titre`, `titre_en`, `resume`, `resume_en`, `fichier_manuscrit`, `statut`, `est_en_avant`, `date_soumission`, `date_publication`, `id_auteur`) VALUES
(1, 'caleb', NULL, 'Caleb etudiant en L3 IRT esgis Bénin est bisexuel travail pour mybasketzone où il est sous employé ', NULL, '../uploads/manuscrit_68d164efbe9ff9.33652375.docx', 'accepté', 0, '2025-09-22 15:02:08', NULL, 1),
(2, 'Alois', NULL, 'Caleb etudiant en L3 IRT esgis Bénin est bisexuel travail pour mybasketzone où il est sous employé ', NULL, '../uploads/manuscrit_68d1cf3cd0ec90.87634367.pdf', 'accepté', 0, '2025-09-22 22:35:41', NULL, 2),
(3, 'Christopher', NULL, 'Caleb etudiant en L3 IRT esgis Bénin est bisexuel travail pour mybasketzone où il est sous employé ', NULL, '../uploads/manuscrit_68d1d00cad9ff3.29187027.docx', 'accepté', 0, '2025-09-22 22:39:09', NULL, 3);

-- --------------------------------------------------------

--
-- Structure de la table `auteur`
--

CREATE TABLE `auteur` (
  `id_auteur` int NOT NULL,
  `nom` varchar(355) NOT NULL,
  `prenom` varchar(355) NOT NULL,
  `institution` varchar(500) NOT NULL,
  `email` varchar(355) NOT NULL,
  `telephone` varchar(355) NOT NULL,
  `password` varchar(355) NOT NULL,
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `auteur`
--

INSERT INTO `auteur` (`id_auteur`, `nom`, `prenom`, `institution`, `email`, `telephone`, `password`, `date`) VALUES
(1, 'MESSOUHOUNOU', 'Caleb ', 'LAVIE', 'vianneyhoueho@gmail.com', '0160888668', '$2y$12$7mTBWztUDAPvGGYTQa9fSemkijc2kVaaBmc5f/pU6v8JMpfrNc4aW', '2025-09-22 15:02:08'),
(2, 'MESSOUHOUNOU', 'Caleb ', 'LAVIE', 'vianneyhou@gmail.com', '0160888668', '$2y$12$1KLcYfGnA/vP13RZK7hW8u3XDkiHMTdajbsR9eiTBIFkPbwCtMM.S', '2025-09-22 22:35:41'),
(3, 'MESSOUHOUNOU', 'Caleb ', 'LAVIE', 'vianneyueho@gmail.com', '0160888668', '$2y$12$uvu2HyWY1Vblc/KGVVzUyuh3g9EPl9An7vMVRbdTDj/yMkz4m.Xxu', '2025-09-22 22:39:09');

-- --------------------------------------------------------

--
-- Structure de la table `categories`
--

CREATE TABLE `categories` (
  `id_categorie` int NOT NULL,
  `nom` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Commentaires_Relecture`
--

CREATE TABLE `Commentaires_Relecture` (
  `id_commentaire` int NOT NULL,
  `id_article` int NOT NULL,
  `id_relecteur` int NOT NULL,
  `commentaire` text,
  `recommandation` enum('accepter','revisions_mineures','revisions_majeures','refuser') DEFAULT NULL,
  `version_manuscrit` varchar(255) DEFAULT NULL,
  `date_commentaire` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `ContenuStatique`
--

CREATE TABLE `ContenuStatique` (
  `cle` varchar(50) NOT NULL,
  `valeur` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `ContenuStatique`
--

INSERT INTO `ContenuStatique` (`cle`, `valeur`) VALUES
('abouten', 'ok'),
('aboutfr', 'Ok'),
('contacten', 'ok'),
('contactfr', 'ok');

-- --------------------------------------------------------

--
-- Structure de la table `Historique_Soumission`
--

CREATE TABLE `Historique_Soumission` (
  `id_historique` int NOT NULL,
  `id_article` int NOT NULL,
  `id_utilisateur` int DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `date_action` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Liaison_Article_Categorie`
--

CREATE TABLE `Liaison_Article_Categorie` (
  `id_liaison` int NOT NULL,
  `id_article` int NOT NULL,
  `id_categorie` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Liaison_Article_Mot_Cle`
--

CREATE TABLE `Liaison_Article_Mot_Cle` (
  `id_liaison` int NOT NULL,
  `id_article` int NOT NULL,
  `id_mot_cle` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Liaison_Article_Mot_Cle`
--

INSERT INTO `Liaison_Article_Mot_Cle` (`id_liaison`, `id_article`, `id_mot_cle`) VALUES
(1, 1, 1),
(2, 2, 1),
(3, 3, 1);

-- --------------------------------------------------------

--
-- Structure de la table `Mots_Cles`
--

CREATE TABLE `Mots_Cles` (
  `id_mot_cle` int NOT NULL,
  `mot_cle` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Mots_Cles`
--

INSERT INTO `Mots_Cles` (`id_mot_cle`, `mot_cle`) VALUES
(1, 'Caleb');

-- --------------------------------------------------------

--
-- Structure de la table `Statistiques_Articles`
--

CREATE TABLE `Statistiques_Articles` (
  `id_stat` int NOT NULL,
  `id_article` int NOT NULL,
  `nombre_telechargements` int DEFAULT '0',
  `date_mise_a_jour` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Structure de la table `Utilisateurs`
--

CREATE TABLE `Utilisateurs` (
  `id_utilisateur` int NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `mot_de_passe` varchar(255) NOT NULL,
  `role` enum('administrateur','editeur','relecteur') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Déchargement des données de la table `Utilisateurs`
--

INSERT INTO `Utilisateurs` (`id_utilisateur`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`) VALUES
(1, 'Admin', 'Super', 'admin@test.com', '$2y$12$MOU7/d2sLKHaeANW8ztiF.kXoTTMYDeN8vAT8bfAcSmwDiguSUgHi', 'administrateur'),
(2, 'Editeur', 'Super', 'editeur@test.com', '$2y$12$Lc62Iu2UdRDVx0vBZyNrpu7Cs1BSmxNrsHZDtlWryEZXok5YEMf9m', 'editeur'),
(3, 'Relecteur', 'Super', 'relecteur@test.com', '$2y$12$MIpnraTKj85Kmz3r3IEhd.I71/REYxX6Cnq2tSA2H5pgDLhcaSJ1m', 'relecteur');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `Articles`
--
ALTER TABLE `Articles`
  ADD PRIMARY KEY (`id_article`),
  ADD KEY `id_auteur` (`id_auteur`);

--
-- Index pour la table `auteur`
--
ALTER TABLE `auteur`
  ADD PRIMARY KEY (`id_auteur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Index pour la table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id_categorie`),
  ADD UNIQUE KEY `nom` (`nom`);

--
-- Index pour la table `Commentaires_Relecture`
--
ALTER TABLE `Commentaires_Relecture`
  ADD PRIMARY KEY (`id_commentaire`),
  ADD KEY `id_article` (`id_article`),
  ADD KEY `id_relecteur` (`id_relecteur`);

--
-- Index pour la table `ContenuStatique`
--
ALTER TABLE `ContenuStatique`
  ADD PRIMARY KEY (`cle`);

--
-- Index pour la table `Historique_Soumission`
--
ALTER TABLE `Historique_Soumission`
  ADD PRIMARY KEY (`id_historique`),
  ADD KEY `id_article` (`id_article`),
  ADD KEY `id_utilisateur` (`id_utilisateur`);

--
-- Index pour la table `Liaison_Article_Categorie`
--
ALTER TABLE `Liaison_Article_Categorie`
  ADD PRIMARY KEY (`id_liaison`),
  ADD UNIQUE KEY `id_article` (`id_article`,`id_categorie`),
  ADD KEY `id_categorie` (`id_categorie`);

--
-- Index pour la table `Liaison_Article_Mot_Cle`
--
ALTER TABLE `Liaison_Article_Mot_Cle`
  ADD PRIMARY KEY (`id_liaison`),
  ADD UNIQUE KEY `id_article` (`id_article`,`id_mot_cle`),
  ADD KEY `id_mot_cle` (`id_mot_cle`);

--
-- Index pour la table `Mots_Cles`
--
ALTER TABLE `Mots_Cles`
  ADD PRIMARY KEY (`id_mot_cle`),
  ADD UNIQUE KEY `mot_cle` (`mot_cle`);

--
-- Index pour la table `Statistiques_Articles`
--
ALTER TABLE `Statistiques_Articles`
  ADD PRIMARY KEY (`id_stat`),
  ADD UNIQUE KEY `id_article` (`id_article`);

--
-- Index pour la table `Utilisateurs`
--
ALTER TABLE `Utilisateurs`
  ADD PRIMARY KEY (`id_utilisateur`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `Articles`
--
ALTER TABLE `Articles`
  MODIFY `id_article` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `auteur`
--
ALTER TABLE `auteur`
  MODIFY `id_auteur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `categories`
--
ALTER TABLE `categories`
  MODIFY `id_categorie` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Commentaires_Relecture`
--
ALTER TABLE `Commentaires_Relecture`
  MODIFY `id_commentaire` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Historique_Soumission`
--
ALTER TABLE `Historique_Soumission`
  MODIFY `id_historique` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Liaison_Article_Categorie`
--
ALTER TABLE `Liaison_Article_Categorie`
  MODIFY `id_liaison` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Liaison_Article_Mot_Cle`
--
ALTER TABLE `Liaison_Article_Mot_Cle`
  MODIFY `id_liaison` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `Mots_Cles`
--
ALTER TABLE `Mots_Cles`
  MODIFY `id_mot_cle` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `Statistiques_Articles`
--
ALTER TABLE `Statistiques_Articles`
  MODIFY `id_stat` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `Utilisateurs`
--
ALTER TABLE `Utilisateurs`
  MODIFY `id_utilisateur` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `Articles`
--
ALTER TABLE `Articles`
  ADD CONSTRAINT `Articles_ibfk_1` FOREIGN KEY (`id_auteur`) REFERENCES `auteur` (`id_auteur`);

--
-- Contraintes pour la table `Commentaires_Relecture`
--
ALTER TABLE `Commentaires_Relecture`
  ADD CONSTRAINT `Commentaires_Relecture_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `Articles` (`id_article`),
  ADD CONSTRAINT `Commentaires_Relecture_ibfk_2` FOREIGN KEY (`id_relecteur`) REFERENCES `Utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `Historique_Soumission`
--
ALTER TABLE `Historique_Soumission`
  ADD CONSTRAINT `Historique_Soumission_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `Articles` (`id_article`),
  ADD CONSTRAINT `Historique_Soumission_ibfk_2` FOREIGN KEY (`id_utilisateur`) REFERENCES `Utilisateurs` (`id_utilisateur`);

--
-- Contraintes pour la table `Liaison_Article_Categorie`
--
ALTER TABLE `Liaison_Article_Categorie`
  ADD CONSTRAINT `Liaison_Article_Categorie_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `Articles` (`id_article`),
  ADD CONSTRAINT `Liaison_Article_Categorie_ibfk_2` FOREIGN KEY (`id_categorie`) REFERENCES `categories` (`id_categorie`);

--
-- Contraintes pour la table `Liaison_Article_Mot_Cle`
--
ALTER TABLE `Liaison_Article_Mot_Cle`
  ADD CONSTRAINT `Liaison_Article_Mot_Cle_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `Articles` (`id_article`),
  ADD CONSTRAINT `Liaison_Article_Mot_Cle_ibfk_2` FOREIGN KEY (`id_mot_cle`) REFERENCES `Mots_Cles` (`id_mot_cle`);

--
-- Contraintes pour la table `Statistiques_Articles`
--
ALTER TABLE `Statistiques_Articles`
  ADD CONSTRAINT `Statistiques_Articles_ibfk_1` FOREIGN KEY (`id_article`) REFERENCES `Articles` (`id_article`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
