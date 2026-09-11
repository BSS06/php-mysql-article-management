-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Gazdă: 127.0.0.1
-- Timp de generare: mai 03, 2026 la 12:30 AM
-- Versiune server: 10.4.32-MariaDB
-- Versiune PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Bază de date: `gestiune_utilizatori`
--

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `permisiuni`
--

CREATE TABLE `permisiuni` (
  `id_permisiune` int(11) NOT NULL,
  `id_rol` int(11) NOT NULL,
  `actiune` varchar(60) NOT NULL,
  `permis` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Eliminarea datelor din tabel `permisiuni`
--

INSERT INTO `permisiuni` (`id_permisiune`, `id_rol`, `actiune`, `permis`) VALUES
(1, 1, 'create_article', 1),
(2, 1, 'edit_any_article', 1),
(3, 1, 'delete_article', 1),
(4, 1, 'manage_users', 1),
(5, 1, 'manage_roles', 1),
(6, 1, 'delete_comment', 1),
(7, 1, 'view_analytics', 1),
(8, 2, 'create_article', 1),
(9, 2, 'edit_own_article', 1),
(10, 2, 'publish_article', 1),
(11, 2, 'archive_article', 1),
(12, 2, 'delete_comment', 0),
(13, 2, 'manage_users', 0),
(14, 3, 'create_article', 0),
(15, 3, 'add_comment', 1),
(16, 3, 'rate_article', 1),
(17, 3, 'view_public_content', 1),
(18, 1, 'view_dashboard', 1),
(19, 1, 'publish_article', 1),
(20, 1, 'manage_comments', 1),
(21, 1, 'manage_categories', 1),
(22, 2, 'view_dashboard', 1),
(23, 2, 'manage_roles', 0),
(24, 2, 'edit_any_article', 0),
(25, 2, 'delete_article', 0),
(26, 2, 'manage_comments', 0),
(27, 2, 'view_analytics', 1),
(28, 2, 'manage_categories', 0),
(29, 3, 'view_dashboard', 0),
(30, 3, 'manage_users', 0),
(31, 3, 'manage_roles', 0),
(32, 3, 'edit_any_article', 0),
(33, 3, 'delete_article', 0),
(34, 3, 'publish_article', 0),
(35, 3, 'manage_comments', 0),
(36, 3, 'view_analytics', 0),
(37, 3, 'manage_categories', 0);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `roluri`
--

CREATE TABLE `roluri` (
  `id_rol` int(11) NOT NULL,
  `denumire_rol` enum('administrator','redactor','cititor') NOT NULL,
  `descriere` text DEFAULT NULL,
  `creat_la` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Eliminarea datelor din tabel `roluri`
--

INSERT INTO `roluri` (`id_rol`, `denumire_rol`, `descriere`, `creat_la`) VALUES
(1, 'administrator', 'Acces complet: CRUD pe toate entitățile, gestionare utilizatori', '2026-03-19 15:23:49'),
(2, 'redactor', 'Poate crea, edita, publica și arhiva articole proprii', '2026-03-19 15:23:49'),
(3, 'cititor', 'Poate vizualiza articole publicate și lăsa comentarii/evaluări', '2026-03-19 15:23:49');

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `sesiuni`
--

CREATE TABLE `sesiuni` (
  `id_sesiune` int(11) NOT NULL,
  `id_utilizator` int(11) NOT NULL,
  `token` varchar(512) NOT NULL,
  `ip_adresa` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `creat_la` datetime NOT NULL DEFAULT current_timestamp(),
  `expira_la` datetime NOT NULL,
  `activa` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Eliminarea datelor din tabel `sesiuni`
--

INSERT INTO `sesiuni` (`id_sesiune`, `id_utilizator`, `token`, `ip_adresa`, `user_agent`, `creat_la`, `expira_la`, `activa`) VALUES
(1, 1, 'e886b71d99fd926741da35cb30c2e658aaedbb66df4685148be9e6e2b972e71b', NULL, NULL, '2026-03-22 17:30:00', '2026-03-23 17:30:00', 1),
(2, 3, 'bd65d69564bae1997e8e9f7a63daa28878c637ce49b391895efc09641c7bc0a0', NULL, NULL, '2026-03-21 09:45:00', '2026-03-22 09:45:00', 1),
(3, 7, '79f9ac400eda15d6a215c4fd82229a3b6f70b02bdb87a7c7e892d8ade53de3f4', NULL, NULL, '2026-03-22 10:30:00', '2026-03-22 16:30:00', 0);

-- --------------------------------------------------------

--
-- Structură tabel pentru tabel `utilizatori`
--

CREATE TABLE `utilizatori` (
  `id_utilizator` int(11) NOT NULL,
  `nume` varchar(50) NOT NULL,
  `prenume` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `parola_hash` varchar(255) NOT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  `activ` tinyint(1) NOT NULL DEFAULT 1,
  `data_inregistrare` datetime NOT NULL DEFAULT current_timestamp(),
  `ultima_autentificare` datetime DEFAULT NULL,
  `id_rol` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Eliminarea datelor din tabel `utilizatori`
--

INSERT INTO `utilizatori` (`id_utilizator`, `nume`, `prenume`, `email`, `parola_hash`, `avatar_url`, `activ`, `data_inregistrare`, `ultima_autentificare`, `id_rol`) VALUES
(1, 'Brodovoi', 'Serghei', 'admin@portal.md', '4813494d137e1631bba301d5acab6e7bb7aa74ce1185d456565ef51d737677b2', NULL, 1, '2026-03-19 15:25:44', '2026-05-03 01:27:35', 1),
(2, 'Ciobanu', 'Maria', 'redactor@portal.md', '4813494d137e1631bba301d5acab6e7bb7aa74ce1185d456565ef51d737677b2', NULL, 1, '2026-03-19 15:25:44', '2026-05-03 01:27:49', 2),
(3, 'Rusu', 'Andrei', 'andrei@portal.md', '4813494d137e1631bba301d5acab6e7bb7aa74ce1185d456565ef51d737677b2', NULL, 1, '2026-03-19 15:25:44', '2026-04-29 20:11:24', 3),
(4, 'Munteanu', 'Elena', 'elena@portal.md', '4813494d137e1631bba301d5acab6e7bb7aa74ce1185d456565ef51d737677b2', NULL, 1, '2026-03-19 15:25:44', NULL, 2),
(5, 'Test', 'User', 'test_integritate_1774193022@portal.md', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', NULL, 0, '2026-03-22 17:23:42', NULL, 3),
(6, 'Grama', 'Vasile', 'vasile.grama@portal.md', '4813494d137e1631bba301d5acab6e7bb7aa74ce1185d456565ef51d737677b2', NULL, 0, '2025-05-20 09:00:00', NULL, 2),
(7, 'Test', 'User', 'test_integritate_1774193065@portal.md', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', NULL, 0, '2026-03-22 17:24:25', NULL, 3),
(9, 'Test', 'User', 'test_integritate_1774193069@portal.md', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', NULL, 0, '2026-03-22 17:24:29', NULL, 3),
(10, 'Sirbu', 'Ana', 'ana.sirbu@portal.md', '4813494d137e1631bba301d5acab6e7bb7aa74ce1185d456565ef51d737677b2', NULL, 0, '2025-11-20 12:00:00', NULL, 3),
(11, 'Test', 'User', 'test_integritate_1774193109@portal.md', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', NULL, 0, '2026-03-22 17:25:09', NULL, 3),
(13, 'Test', 'User', 'test_integritate_1774193113@portal.md', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', NULL, 0, '2026-03-22 17:25:13', NULL, 3),
(15, 'Test', 'User', 'test_integritate_1774193156@portal.md', 'ecd71870d1963316a97e3ac3408c9835ad8cf0f3c1bc703527c30265534f75ae', NULL, 0, '2026-03-22 17:25:56', NULL, 3),
(65, 'Ionescu', 'Mihai', 'cititor@portal.md', '4813494d137e1631bba301d5acab6e7bb7aa74ce1185d456565ef51d737677b2', NULL, 1, '2026-05-03 01:26:19', '2026-05-03 01:27:09', 3);

--
-- Indexuri pentru tabele eliminate
--

--
-- Indexuri pentru tabele `permisiuni`
--
ALTER TABLE `permisiuni`
  ADD PRIMARY KEY (`id_permisiune`),
  ADD UNIQUE KEY `uq_rol_actiune` (`id_rol`,`actiune`);

--
-- Indexuri pentru tabele `roluri`
--
ALTER TABLE `roluri`
  ADD PRIMARY KEY (`id_rol`),
  ADD UNIQUE KEY `denumire_rol` (`denumire_rol`);

--
-- Indexuri pentru tabele `sesiuni`
--
ALTER TABLE `sesiuni`
  ADD PRIMARY KEY (`id_sesiune`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `idx_token` (`token`),
  ADD KEY `idx_util` (`id_utilizator`);

--
-- Indexuri pentru tabele `utilizatori`
--
ALTER TABLE `utilizatori`
  ADD PRIMARY KEY (`id_utilizator`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `idx_email` (`email`),
  ADD KEY `idx_rol` (`id_rol`);

--
-- AUTO_INCREMENT pentru tabele eliminate
--

--
-- AUTO_INCREMENT pentru tabele `permisiuni`
--
ALTER TABLE `permisiuni`
  MODIFY `id_permisiune` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT pentru tabele `roluri`
--
ALTER TABLE `roluri`
  MODIFY `id_rol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pentru tabele `sesiuni`
--
ALTER TABLE `sesiuni`
  MODIFY `id_sesiune` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pentru tabele `utilizatori`
--
ALTER TABLE `utilizatori`
  MODIFY `id_utilizator` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- Constrângeri pentru tabele eliminate
--

--
-- Constrângeri pentru tabele `permisiuni`
--
ALTER TABLE `permisiuni`
  ADD CONSTRAINT `fk_perm_rol` FOREIGN KEY (`id_rol`) REFERENCES `roluri` (`id_rol`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constrângeri pentru tabele `sesiuni`
--
ALTER TABLE `sesiuni`
  ADD CONSTRAINT `fk_ses_util` FOREIGN KEY (`id_utilizator`) REFERENCES `utilizatori` (`id_utilizator`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constrângeri pentru tabele `utilizatori`
--
ALTER TABLE `utilizatori`
  ADD CONSTRAINT `fk_util_rol` FOREIGN KEY (`id_rol`) REFERENCES `roluri` (`id_rol`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
