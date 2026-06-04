-- Kompletní import databáze pro Cestovní kancelář Venturo
-- Obsahuje definici tabulek a testovací data

CREATE DATABASE IF NOT EXISTS `venturo` DEFAULT CHARACTER SET utf8 COLLATE utf8_czech_ci;
USE `venturo`;

SET FOREIGN_KEY_CHECKS = 0;

-- Smazání tabulek, pokud již existují (v pořadí od závislých po nezávislé)
DROP TABLE IF EXISTS `rezervace`;
DROP TABLE IF EXISTS `terminy`;
DROP TABLE IF EXISTS `zajezdy`;
DROP TABLE IF EXISTS `destinace`;
DROP TABLE IF EXISTS `letiste`;
DROP TABLE IF EXISTS `staty`;
DROP TABLE IF EXISTS `uzivatele`;

SET FOREIGN_KEY_CHECKS = 1;

-- 1. Tabulka: staty
CREATE TABLE `staty` (
  `id_statu` int(11) NOT NULL AUTO_INCREMENT,
  `nazev` varchar(100) NOT NULL,
  `banner` varchar(255) DEFAULT '',
  PRIMARY KEY (`id_statu`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- 2. Tabulka: letiste
CREATE TABLE `letiste` (
  `iata` varchar(3) NOT NULL,
  `id_statu` int(11) NOT NULL,
  `mesto` varchar(100) NOT NULL,
  PRIMARY KEY (`iata`),
  KEY `id_statu` (`id_statu`),
  CONSTRAINT `fk_letiste_staty` FOREIGN KEY (`id_statu`) REFERENCES `staty` (`id_statu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- 3. Tabulka: destinace
CREATE TABLE `destinace` (
  `id_destinace` int(11) NOT NULL AUTO_INCREMENT,
  `id_statu` int(11) NOT NULL,
  `nazev_mesta` varchar(100) NOT NULL,
  PRIMARY KEY (`id_destinace`),
  KEY `id_statu` (`id_statu`),
  CONSTRAINT `fk_destinace_staty` FOREIGN KEY (`id_statu`) REFERENCES `staty` (`id_statu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- 4. Tabulka: zajezdy
CREATE TABLE `zajezdy` (
  `id_zajezdu` int(11) NOT NULL AUTO_INCREMENT,
  `id_destinace` int(11) NOT NULL,
  `hotel` varchar(150) NOT NULL,
  `obrazky` varchar(255) DEFAULT '',
  `popis` text DEFAULT NULL,
  `cena` int(11) NOT NULL,
  `strava` varchar(50) NOT NULL,
  PRIMARY KEY (`id_zajezdu`),
  KEY `id_destinace` (`id_destinace`),
  CONSTRAINT `fk_zajezdy_destinace` FOREIGN KEY (`id_destinace`) REFERENCES `destinace` (`id_destinace`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- 5. Tabulka: terminy
CREATE TABLE `terminy` (
  `id_terminu` int(11) NOT NULL AUTO_INCREMENT,
  `id_zajezdu` int(11) NOT NULL,
  `datum_od` date NOT NULL,
  `datum_do` date NOT NULL,
  `kapacita` int(11) NOT NULL,
  PRIMARY KEY (`id_terminu`),
  KEY `id_zajezdu` (`id_zajezdu`),
  CONSTRAINT `fk_terminy_zajezdy` FOREIGN KEY (`id_zajezdu`) REFERENCES `zajezdy` (`id_zajezdu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- 6. Tabulka: uzivatele
CREATE TABLE `uzivatele` (
  `id_uzivatele` int(11) NOT NULL AUTO_INCREMENT,
  `jmeno` varchar(50) NOT NULL,
  `prijmeni` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `heslo` varchar(255) NOT NULL,
  `role` enum('user','admin') NOT NULL DEFAULT 'user',
  `vytvoren` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_uzivatele`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

-- 7. Tabulka: rezervace
CREATE TABLE `rezervace` (
  `id_rezervace` int(11) NOT NULL AUTO_INCREMENT,
  `id_uzivatele` int(11) NOT NULL,
  `id_terminu` int(11) NOT NULL,
  `pocet_osob` int(11) NOT NULL,
  `stav` enum('čekající','potvrzená','zrušená') NOT NULL DEFAULT 'čekající',
  `datum_rezervace` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_rezervace`),
  KEY `id_uzivatele` (`id_uzivatele`),
  KEY `id_terminu` (`id_terminu`),
  CONSTRAINT `fk_rezervace_uzivatele` FOREIGN KEY (`id_uzivatele`) REFERENCES `uzivatele` (`id_uzivatele`) ON DELETE CASCADE,
  CONSTRAINT `fk_rezervace_terminy` FOREIGN KEY (`id_terminu`) REFERENCES `terminy` (`id_terminu`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;


-- =========================================================
-- DATA PRO TABULKU staty
-- =========================================================
INSERT INTO staty (id_statu, nazev, banner) VALUES
(1, 'Albánie', ''), (2, 'Antigua a Barbuda', ''), (3, 'Argentina', ''), (4, 'Arménie', ''), (5, 'Austrálie', ''),
(6, 'Rakousko', ''), (7, 'Bahamy', ''), (8, 'Bahrajn', ''), (9, 'Barbados', ''), (10, 'Bělorusko', ''),
(11, 'Belgie', ''), (12, 'Belize', ''), (13, 'Bolívie', ''), (14, 'Bosna a Hercegovina', ''), (15, 'Botswana', ''),
(16, 'Brazílie', ''), (17, 'Bulharsko', ''), (18, 'Kapverdy', ''), (19, 'Kanada', ''), (20, 'Kajmanské ostrovy', ''),
(21, 'Chile', ''), (22, 'Čína', ''), (23, 'Kolumbie', ''), (24, 'Cookovy ostrovy', ''), (25, 'Kostarika', ''),
(26, 'Chorvatsko', ''), (27, 'Kuba', ''), (28, 'Kypr', ''), (29, 'Česko', ''), (30, 'Dánsko', ''),
(31, 'Dominikánská republika', ''), (32, 'Egypt', ''), (33, 'Estonsko', ''), (34, 'Etiopie', ''), (35, 'Fidži', ''),
(36, 'Finsko', ''), (37, 'Francie', ''), (38, 'Gruzie', ''), (39, 'Německo', ''), (40, 'Řecko', ''),
(41, 'Grenada', ''), (42, 'Maďarsko', ''), (43, 'Island', ''), (44, 'Indie', ''), (45, 'Indonésie', ''),
(46, 'Irsko', ''), (47, 'Izrael', ''), (48, 'Itálie', ''), (49, 'Jamajka', ''), (50, 'Japonsko', ''),
(51, 'Jordánsko', ''), (52, 'Keňa', ''), (53, 'Jižní Korea', ''), (54, 'Kuvajt', ''), (55, 'Lotyšsko', ''),
(56, 'Libanon', ''), (57, 'Lichtenštejnsko', ''), (58, 'Litva', ''), (59, 'Lucembursko', ''), (60, 'Madagaskar', ''),
(61, 'Malajsie', ''), (62, 'Maledivy', ''), (63, 'Malta', ''), (64, 'Marshallovy ostrovy', ''), (65, 'Mauricius', ''),
(66, 'Mexiko', ''), (67, 'Moldavsko', ''), (68, 'Monako', ''), (69, 'Mongolsko', ''), (70, 'Černá Hora', ''),
(71, 'Maroko', ''), (72, 'Nepál', ''), (73, 'Nizozemsko', ''), (74, 'Nový Zéland', ''), (75, 'Severní Makedonie', ''),
(76, 'Norsko', ''), (77, 'Omán', ''), (78, 'Panama', ''), (79, 'Peru', ''), (80, 'Filipíny', ''),
(81, 'Polsko', ''), (82, 'Portugalsko', ''), (83, 'Katar', ''), (84, 'Rumunsko', ''), (85, 'Rusko', ''),
(86, 'San Marino', ''), (87, 'Saúdská Arábie', ''), (88, 'Senegal', ''), (89, 'Srbsko', ''), (90, 'Seychely', ''),
(91, 'Singapur', ''), (92, 'Slovensko', ''), (93, 'Slovinsko', ''), (94, 'Jihoafrická republika', ''), (95, 'Španělsko', ''),
(96, 'Srí Lanka', ''), (97, 'Švédsko', ''), (98, 'Švýcarsko', ''), (99, 'Tanzanie', ''), (100, 'Thajsko', ''),
(101, 'Tunisko', ''), (102, 'Turecko', ''), (103, 'Ukrajina', ''), (104, 'Spojené království', ''), (105, 'Vietnam', ''),
(106, 'Spojené arabské emiráty', '');


-- =========================================================
-- DATA PRO TABULKU letiste
-- =========================================================
INSERT INTO letiste (iata, id_statu, mesto) VALUES
('PRG', 29, 'Praha'),
('BRQ', 29, 'Brno'),
('OSR', 29, 'Ostrava'),
('PED', 29, 'Pardubice'),
('JCL', 29, 'České Budějovice'),
('KLV', 29, 'Karlovy Vary'),
('BTS', 92, 'Bratislava'),
('KSC', 92, 'Košice'),
('TAT', 92, 'Poprad/Tatry'),
('LNZ', 6, 'Linz'),
('SZG', 6, 'Salzburg'),
('VIE', 6, 'Vídeň'),
('KTW', 81, 'Katowice'),
('KRK', 81, 'Krakow'),
('WAW', 81, 'Varšava - F. Chopin'),
('BUD', 42, 'Budapešť'),
('BER', 39, 'Berlin'),
('DRS', 39, 'Drážďany'),
('DUS', 39, 'Duesseldorf'),
('ERF', 39, 'Erfurt'),
('FRA', 39, 'Frankfurt'),
('HAM', 39, 'Hamburg'),
('LEJ', 39, 'Lipsko'),
('MUC', 39, 'Mnichov'),
('NUE', 39, 'Norimberk'),
('STR', 39, 'Stuttgart');


-- =========================================================
-- DATA PRO TABULKU destinace
-- =========================================================
INSERT INTO destinace (id_destinace, id_statu, nazev_mesta) VALUES
(1, 1, 'Tirana'), (2, 1, 'Albánská riviéra'), (3, 2, 'St. John''s'), (4, 2, 'English Harbour'),
(5, 3, 'Buenos Aires'), (6, 3, 'Patagonie'), (7, 4, 'Jerevan'), (8, 4, 'Sjunik'),
(9, 5, 'Sydney'), (10, 5, 'Queensland'), (11, 5, 'Severní teritorium'),
(12, 6, 'Vídeň'), (13, 6, 'Salcburk'), (14, 6, 'Rakouské Alpy'),
(15, 7, 'Nassau'), (16, 7, 'Paradise Island'), (17, 8, 'Manáma'),
(18, 9, 'Bridgetown'), (19, 9, 'Carlisle Bay'), (20, 10, 'Minsk'), (21, 10, 'Brest'),
(22, 11, 'Brusel'), (23, 11, 'Bruggy'), (24, 12, 'Cayo'), (25, 12, 'Bariérový útes Belize'),
(26, 13, 'Salar de Uyuni'), (27, 13, 'La Paz'), (28, 14, 'Sarajevo'), (29, 14, 'Mostar'),
(30, 15, 'Delta Okavanga'), (31, 15, 'Chobe'), (32, 16, 'Rio de Janeiro'), (33, 16, 'Amazonie'),
(34, 17, 'Sofie'), (35, 17, 'Slunečné pobřeží'), (36, 17, 'Plovdiv'), (37, 18, 'Sal'), (38, 18, 'Boa Vista'),
(39, 19, 'Toronto'), (40, 19, 'Alberta'), (41, 19, 'Ontario'), (42, 20, 'Grand Cayman'),
(43, 21, 'Atacama'), (44, 21, 'Patagonie'), (45, 22, 'Peking'), (46, 22, 'Si-an'),
(47, 23, 'Cartagena'), (48, 23, 'Medellín'), (49, 23, 'Tayrona'), (50, 24, 'Rarotonga'), (51, 24, 'Aitutaki'),
(52, 25, 'Puntarenas'), (53, 25, 'Alajuela'), (54, 26, 'Dubrovník'), (55, 26, 'Dalmácie'), (56, 26, 'Lika-Senj'),
(57, 27, 'Havana'), (58, 27, 'Varadero'), (59, 28, 'Paphos'), (60, 28, 'Ayia Napa'),
(61, 29, 'Praha'), (62, 29, 'Český Krumlov'), (63, 30, 'Kodaň'), (64, 30, 'Billund'),
(65, 31, 'Punta Cana'), (66, 31, 'Santo Domingo'), (67, 32, 'Káhira'), (68, 32, 'Luxor'), (69, 32, 'Hurghada'),
(70, 33, 'Tallinn'), (71, 34, 'Lalibela'), (72, 34, 'Danakil'), (73, 35, 'Mamanuca'), (74, 35, 'Yasawa'),
(75, 36, 'Helsinky'), (76, 36, 'Laponsko'), (77, 37, 'Paříž'), (78, 37, 'Francouzská riviéra'), (79, 37, 'Údolí Loiry'),
(80, 38, 'Tbilisi'), (81, 38, 'Kazbegi'), (82, 39, 'Berlín'), (83, 39, 'Bavorsko'),
(84, 40, 'Athény'), (85, 40, 'Santorini'), (86, 40, 'Kréta'), (87, 41, 'St. George''s'), (88, 41, 'Grand Anse'),
(89, 42, 'Budapešť'), (90, 42, 'Balaton'), (91, 43, 'Reykjanes'), (92, 43, 'Jižní Island'),
(93, 44, 'Ágra'), (94, 44, 'Nové Dillí'), (95, 44, 'Rádžasthán'), (96, 45, 'Bali'), (97, 45, 'Jáva'), (98, 45, 'Jakarta'),
(99, 46, 'Dublin'), (100, 46, 'hrabství Clare'), (101, 47, 'Jeruzalém'), (102, 47, 'Tel Aviv'),
(103, 48, 'Řím'), (104, 48, 'Benátky'), (105, 48, 'Florencie'), (106, 48, 'Amalfské pobřeží'),
(107, 49, 'Montego Bay'), (108, 49, 'Negril'), (109, 50, 'Tokio'), (110, 50, 'Kjóto'),
(111, 51, 'Petra'), (112, 51, 'Wádí Rum'), (113, 52, 'Masai Mara'), (114, 52, 'Mombasa'),
(115, 53, 'Soul'), (116, 53, 'Čedžu'), (117, 54, 'Kuwait City'), (118, 55, 'Riga'),
(119, 56, 'Bejrút'), (120, 56, 'Byblos'), (121, 57, 'Vaduz'), (122, 58, 'Vilnius'), (123, 58, 'Trakai'),
(124, 59, 'Lucemburk'), (125, 60, 'Menabe'), (126, 60, 'Isalo'),
(127, 61, 'Kuala Lumpur'), (128, 61, 'Penang'), (129, 61, 'Langkawi'), (130, 62, 'Malé'), (131, 62, 'atoly Malediv'),
(132, 63, 'Valletta'), (133, 63, 'Comino'), (134, 64, 'Majuro'), (135, 64, 'Bikini'),
(136, 65, 'Le Morne'), (137, 65, 'Chamarel'), (138, 66, 'Cancún'), (139, 66, 'Riviéra Maya'),
(140, 66, 'Ciudad de México'), (141, 66, 'Puerto Vallarta'), (142, 67, 'Kišiněv'), (143, 67, 'Cricova'),
(144, 68, 'Monte Carlo'), (145, 68, 'Monaco-Ville'), (146, 69, 'Ulanbátar'), (147, 69, 'Gobi'),
(148, 70, 'Kotorský záliv'), (149, 70, 'Budva'), (150, 71, 'Marrákeš'), (151, 71, 'Fez'), (152, 71, 'Chefchaouen'),
(153, 72, 'Káthmándú'), (154, 72, 'oblast Everestu'), (155, 72, 'oblast Annapurny'),
(156, 73, 'Amsterdam'), (157, 73, 'Lisse'), (158, 74, 'Queenstown'), (159, 74, 'Auckland'), (160, 74, 'Fiordland'),
(161, 75, 'Ohrid'), (162, 75, 'Skopje'), (163, 76, 'Norské fjordy'), (164, 76, 'Oslo'), (165, 76, 'Lofoty'),
(166, 77, 'Maskat'), (167, 77, 'Nizwa'), (168, 78, 'Panama City'), (169, 79, 'Cusco'), (170, 79, 'Posvátné údolí'),
(171, 80, 'Boracay'), (172, 80, 'Palawan'), (173, 81, 'Krakov'), (174, 81, 'Varšava'), (175, 81, 'Osvětim'),
(176, 82, 'Lisabon'), (177, 82, 'Porto'), (178, 82, 'Algarve'), (179, 83, 'Dauhá'),
(180, 84, 'Bukurešť'), (181, 84, 'Transylvánie'), (182, 85, 'Moskva'), (183, 85, 'Petrohrad'), (184, 86, 'San Marino'),
(185, 87, 'Mekka'), (186, 87, 'Medina'), (187, 87, 'Rijád'), (188, 87, 'Al Ula'), (189, 88, 'Dakar'), (190, 88, 'Gorée'),
(191, 89, 'Bělehrad'), (192, 89, 'Novi Sad'), (193, 90, 'La Digue'), (194, 90, 'Mahé'), (195, 91, 'Singapur'),
(196, 92, 'Vysoké Tatry'), (197, 92, 'Bratislava'), (198, 93, 'Bled'), (199, 93, 'Lublaň'),
(200, 94, 'Kapské Město'), (201, 94, 'Mpumalanga'), (202, 95, 'Barcelona'), (203, 95, 'Madrid'), (204, 95, 'Andalusie'),
(205, 96, 'Kandy'), (206, 96, 'oblast Yala'), (207, 97, 'Stockholm'), (208, 97, 'Laponsko'),
(209, 98, 'Zermatt'), (210, 98, 'oblast Jungfrau'), (211, 98, 'Lucern'), (212, 99, 'Serengeti'), (213, 99, 'Zanzibar'),
(214, 100, 'Bangkok'), (215, 100, 'Phuket'), (216, 100, 'Chiang Mai'), (217, 101, 'Tunis'), (218, 101, 'Djerba'),
(219, 102, 'Istanbul'), (220, 102, 'Kappadokie'), (221, 103, 'Kyjev'), (222, 103, 'Lviv'),
(223, 104, 'Londýn'), (224, 104, 'Edinburgh'), (225, 104, 'hrabství Wiltshire'),
(226, 105, 'Hanoj'), (227, 105, 'zátoka Ha Long'), (228, 105, 'Hoi An'), (229, 106, 'Dubaj');


-- =========================================================
-- DATA PRO TABULKY zajezdy, terminy, uzivatele, rezervace
-- =========================================================
INSERT INTO `zajezdy` (`id_zajezdu`, `id_destinace`, `hotel`, `obrazky`, `popis`, `cena`, `strava`) VALUES
(1, 85, 'Santorini Palace Hotel', '/images/santorini.jpg', 'Nádherný čtyřhvězdičkový hotel s výhledem na kalderu, velkým bazénem a skvělým servisem.', 24900, 'Snídaně'),
(2, 86, 'Creta Maris Resort', '/images/kreta.jpg', 'Luxusní pětihvězdičkový plážový resort s aquaparkem, ideální pro rodiny s dětmi.', 32000, 'All Inclusive'),
(3, 69, 'Sunrise Crystal Bay Resort', '/images/egypt.jpg', 'Krásný resort s vlastní soukromou lagunou, skvělým zázemím pro potápění a wellness.', 18500, 'All Inclusive'),
(4, 54, 'Valamar Lacroma Dubrovnik', '/images/dubrovnik.jpg', 'Moderní hotel blízko historického centra Dubrovníku, oceněný pro své wellness služby.', 15400, 'Polopenze'),
(5, 202, 'H10 Marina Barcelona', '/images/barcelona.jpg', 'Stylový městský hotel v blízkosti pláže i památek, střešní bazén s panoramatickým výhledem.', 21900, 'Snídaně'),
(6, 106, 'Hotel Poseidon Positano', '/images/amalfi.jpg', 'Romantické ubytování s dechberoucím výhledem na Positano, rodinná atmosféra a venkovní bazén.', 45000, 'Snídaně'),
(7, 103, 'Hotel Artemide Rome', '/images/rome.jpg', 'Komfortní hotel v srdci Říma, kousek od Kolosea, s úžasnou střešní terasou a oceněnou snídaní.', 19900, 'Snídaně'),
(8, 223, 'The Chesterfield Mayfair London', '/images/london.jpg', 'Klasický britský hotel ve čtvrti Mayfair, luxusní interiéry a tradiční odpolední čaj.', 28900, 'Snídaně');

INSERT INTO `terminy` (`id_terminu`, `id_zajezdu`, `datum_od`, `datum_do`, `kapacita`) VALUES
-- Santorini
(1, 1, '2026-07-10', '2026-07-17', 20),
(2, 1, '2026-08-15', '2026-08-22', 15),
-- Kréta
(3, 2, '2026-07-20', '2026-07-27', 30),
(4, 2, '2026-09-05', '2026-09-12', 25),
-- Hurghada
(5, 3, '2026-10-12', '2026-10-19', 40),
(6, 3, '2026-11-01', '2026-11-08', 50),
-- Dubrovník
(7, 4, '2026-07-01', '2026-07-08', 12),
(8, 4, '2026-08-10', '2026-08-17', 10),
-- Barcelona
(9, 5, '2026-06-15', '2026-06-22', 18),
(10, 5, '2026-09-20', '2026-09-27', 20),
-- Positano
(11, 6, '2026-07-05', '2026-07-12', 8),
(12, 6, '2026-08-01', '2026-08-08', 6);

-- Admin: admin@venturo.cz (heslo: admin)
-- User 1: jan.novak@email.cz (heslo: user)
-- User 2: petra.cerna@seznam.cz (heslo: user)
INSERT INTO `uzivatele` (`id_uzivatele`, `jmeno`, `prijmeni`, `email`, `heslo`, `role`) VALUES
(1, 'Admin', 'Venturo', 'admin@venturo.cz', '$2y$10$J1MZ237HbfakdY0FIzZYC.u88iJ39QqHEIARIVt5pARj0yriocraq', 'admin'),
(2, 'Jan', 'Novák', 'jan.novak@email.cz', '$2y$10$KpfuxD7WiwYZzDsbQSea2O.RWKPPDdVtUTw3Ubg4N.s71F3oWR8Ia', 'user'),
(3, 'Petra', 'Černá', 'petra.cerna@seznam.cz', '$2y$10$KpfuxD7WiwYZzDsbQSea2O.RWKPPDdVtUTw3Ubg4N.s71F3oWR8Ia', 'user');

INSERT INTO `rezervace` (`id_rezervace`, `id_uzivatele`, `id_terminu`, `pocet_osob`, `stav`) VALUES
(1, 2, 5, 2, 'potvrzená'),
(2, 3, 1, 2, 'čekající'),
(3, 2, 7, 4, 'zrušená');
