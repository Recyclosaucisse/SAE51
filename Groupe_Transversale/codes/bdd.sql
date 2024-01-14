-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : fdb1031.eohost.com
-- Généré le : dim. 14 jan. 2024 à 11:23
-- Version du serveur : 8.0.32
-- Version de PHP : 8.1.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `3950337_joomla7df3217c`
--

-- --------------------------------------------------------

--
-- Structure de la table `CAPTEUR`
--

CREATE TABLE `CAPTEUR` (
  `ID` int NOT NULL,
  `ID_PIECE` int NOT NULL,
  `NOM` text COLLATE utf8mb4_general_ci NOT NULL,
  `TYPE` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `CAPTEUR`
--

INSERT INTO `CAPTEUR` (`ID`, `ID_PIECE`, `NOM`, `TYPE`) VALUES
(1, 1, 'DOUCHE', 'PIR'),
(2, 1, 'LAVABO', 'PIR'),
(3, 1, 'WC', 'PIR'),
(4, 1, 'ENTREE_SDB', 'PIR'),
(5, 2, 'LUMINOSITE', 'PHOTORESISTANCE'),
(6, 2, 'ANALYSE SPECTRALE', 'SPECTROMETRE');

-- --------------------------------------------------------

--
-- Structure de la table `LOGEMENT`
--

CREATE TABLE `LOGEMENT` (
  `ID` int NOT NULL,
  `UTILISATEUR` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `ADRESSE` text COLLATE utf8mb4_general_ci NOT NULL,
  `NOM` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `LOGEMENT`
--

INSERT INTO `LOGEMENT` (`ID`, `UTILISATEUR`, `ADRESSE`, `NOM`) VALUES
(1, 'enseignants', '1 Place George Brassens', 'MIB');

-- --------------------------------------------------------

--
-- Structure de la table `MESURE`
--

CREATE TABLE `MESURE` (
  `ID` int NOT NULL,
  `ID_CAPTEUR` int NOT NULL,
  `VALEUR` int NOT NULL,
  `ACTION` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `CRITICITE` text COLLATE utf8mb4_general_ci NOT NULL,
  `TIMESTAMP` timestamp(6) NOT NULL DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `MESURE`
--

INSERT INTO `MESURE` (`ID`, `ID_CAPTEUR`, `VALEUR`, `ACTION`, `CRITICITE`, `TIMESTAMP`) VALUES
(1, 1, 1, '', '1', '2024-01-11 19:04:44.490009'),
(2, 2, 1, '', '1', '2024-01-11 19:04:48.671122'),
(3, 3, 0, '', '1', '2024-01-11 19:04:54.379118'),
(4, 5, 200, 'ALLUMER-LUMIERE', '2', '2024-01-11 19:04:55.521052'),
(5, 2, 1, '', '3', '2024-01-11 19:05:03.200775'),
(6, 1, 1, '', '3', '2024-01-11 19:05:07.700566'),
(7, 5, 525, 'ETEINDRE-LUMIERE', '2', '2024-01-11 19:05:16.427284'),
(8, 6, -26, '', '1', '2024-01-11 19:05:22.207290'),
(9, 6, 5, '', '3', '2024-01-11 19:05:30.202154'),
(10, 6, -36, '', '1', '2024-01-11 19:05:38.298074'),
(11, 4, 0, '', '1', '2024-01-11 19:05:42.569446'),
(12, 1, 0, '', '2', '2024-01-11 19:05:44.284381'),
(13, 2, 1, '', '3', '2024-01-11 19:05:50.846725'),
(14, 3, 1, '', '2', '2024-01-11 19:06:00.891362'),
(15, 4, 1, '', '1', '2024-01-11 19:06:09.432976'),
(16, 5, 400, 'ALLUMER-LUMIERE', '2', '2024-01-11 19:06:17.202163'),
(17, 5, 475, 'ETEINDRE-LUMIERE', '2', '2024-01-11 19:06:26.392518'),
(18, 6, -15, '', '1', '2024-01-11 19:06:32.398374'),
(19, 6, 8, '', '3', '2024-01-11 19:06:41.372933'),
(20, 6, -45, '', '1', '2024-01-11 19:06:45.482477'),
(21, 1, 1, '', '1', '2024-01-11 19:06:47.290044'),
(22, 2, 0, '', '1', '2024-01-11 19:06:49.380331'),
(23, 3, 0, '', '1', '2024-01-11 19:06:59.193666'),
(24, 4, 1, '', '2', '2024-01-11 19:07:07.423136'),
(25, 5, 600, 'ALLUMER-LUMIERE', '2', '2024-01-11 19:07:11.443982'),
(26, 1, 100, 'BATTERIE', '1', '2024-01-11 21:34:34.343240');

-- --------------------------------------------------------

--
-- Structure de la table `PIECE`
--

CREATE TABLE `PIECE` (
  `ID` int NOT NULL,
  `ID_LOGEMENT` int NOT NULL,
  `NOM` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `PIECE`
--

INSERT INTO `PIECE` (`ID`, `ID_LOGEMENT`, `NOM`) VALUES
(1, 1, 'Salle de bain'),
(2, 1, 'Salon');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `CAPTEUR`
--
ALTER TABLE `CAPTEUR`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_PIECE` (`ID_PIECE`);

--
-- Index pour la table `LOGEMENT`
--
ALTER TABLE `LOGEMENT`
  ADD PRIMARY KEY (`ID`);

--
-- Index pour la table `MESURE`
--
ALTER TABLE `MESURE`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_CAPTEUR` (`ID_CAPTEUR`);

--
-- Index pour la table `PIECE`
--
ALTER TABLE `PIECE`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `ID_LOGEMENT` (`ID_LOGEMENT`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `CAPTEUR`
--
ALTER TABLE `CAPTEUR`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT pour la table `LOGEMENT`
--
ALTER TABLE `LOGEMENT`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `MESURE`
--
ALTER TABLE `MESURE`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT pour la table `PIECE`
--
ALTER TABLE `PIECE`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `CAPTEUR`
--
ALTER TABLE `CAPTEUR`
  ADD CONSTRAINT `CAPTEUR_ibfk_1` FOREIGN KEY (`ID_PIECE`) REFERENCES `PIECE` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `MESURE`
--
ALTER TABLE `MESURE`
  ADD CONSTRAINT `MESURE_ibfk_1` FOREIGN KEY (`ID_CAPTEUR`) REFERENCES `CAPTEUR` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `PIECE`
--
ALTER TABLE `PIECE`
  ADD CONSTRAINT `PIECE_ibfk_1` FOREIGN KEY (`ID_LOGEMENT`) REFERENCES `LOGEMENT` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
