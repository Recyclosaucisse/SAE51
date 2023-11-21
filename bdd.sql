-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : fdb1031.eohost.com
-- Généré le : mar. 21 nov. 2023 à 12:56
-- Version du serveur : 8.0.32
-- Version de PHP : 8.1.25

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
(4, 2, 'LUMINOSITE', 'PHOTORESISTANCE'),
(5, 2, 'ANALYSE SPECTRALE', 'SPECTROMETRE');

-- --------------------------------------------------------

--
-- Structure de la table `LOGEMENT`
--

CREATE TABLE `LOGEMENT` (
  `ID` int NOT NULL,
  `ADRESSE` text COLLATE utf8mb4_general_ci NOT NULL,
  `NOM` text COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Déchargement des données de la table `LOGEMENT`
--

INSERT INTO `LOGEMENT` (`ID`, `ADRESSE`, `NOM`) VALUES
(1, '1 Place George Brassens', 'MIB');

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
(1, 1, 1, '', '1', '2023-11-21 12:24:36.624893'),
(2, 2, 1, '', '1', '2023-11-21 12:24:56.314048'),
(3, 3, 0, '', '1', '2023-11-21 12:25:41.556358'),
(4, 4, 200, 'ALLUMER-LUMIERE', '2', '2023-11-21 12:26:18.897278'),
(5, 2, 1, '', '3', '2023-11-21 12:26:55.922981'),
(6, 1, 1, '', '3', '2023-11-21 12:27:04.228814'),
(7, 4, 525, 'ETEINDRE-LUMIERE', '2', '2023-11-21 12:27:23.094483'),
(8, 5, -26, '', '1', '2023-11-21 12:48:41.650020'),
(9, 5, 5, '', '3', '2023-11-21 12:48:57.114240'),
(10, 5, 5, '', '1', '2023-11-21 12:49:40.848112');

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
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT pour la table `LOGEMENT`
--
ALTER TABLE `LOGEMENT`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `MESURE`
--
ALTER TABLE `MESURE`
  MODIFY `ID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

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
