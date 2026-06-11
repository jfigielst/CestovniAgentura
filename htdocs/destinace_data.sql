-- Nejprve vyprázdníme tabulky (pokud už v nich něco je, aby se data neduplikovala)
-- Pozor: pokud už tam máš data, která chceš zachovat, tyto příkazy smaž!
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM destinace;
ALTER TABLE destinace AUTO_INCREMENT = 1;
DELETE FROM staty;
ALTER TABLE staty AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- Vložení států
INSERT INTO staty (id_statu, nazev) VALUES
(1, 'Albánie'), (2, 'Antigua a Barbuda'), (3, 'Argentina'), (4, 'Arménie'), (5, 'Austrálie'),
(6, 'Rakousko'), (7, 'Bahamy'), (8, 'Bahrajn'), (9, 'Barbados'), (10, 'Bělorusko'),
(11, 'Belgie'), (12, 'Belize'), (13, 'Bolívie'), (14, 'Bosna a Hercegovina'), (15, 'Botswana'),
(16, 'Brazílie'), (17, 'Bulharsko'), (18, 'Kapverdy'), (19, 'Kanada'), (20, 'Kajmanské ostrovy'),
(21, 'Chile'), (22, 'Čína'), (23, 'Kolumbie'), (24, 'Cookovy ostrovy'), (25, 'Kostarika'),
(26, 'Chorvatsko'), (27, 'Kuba'), (28, 'Kypr'), (29, 'Česko'), (30, 'Dánsko'),
(31, 'Dominikánská republika'), (32, 'Egypt'), (33, 'Estonsko'), (34, 'Etiopie'), (35, 'Fidži'),
(36, 'Finsko'), (37, 'Francie'), (38, 'Gruzie'), (39, 'Německo'), (40, 'Řecko'),
(41, 'Grenada'), (42, 'Maďarsko'), (43, 'Island'), (44, 'Indie'), (45, 'Indonésie'),
(46, 'Irsko'), (47, 'Izrael'), (48, 'Itálie'), (49, 'Jamajka'), (50, 'Japonsko'),
(51, 'Jordánsko'), (52, 'Keňa'), (53, 'Jižní Korea'), (54, 'Kuvajt'), (55, 'Lotyšsko'),
(56, 'Libanon'), (57, 'Lichtenštejnsko'), (58, 'Litva'), (59, 'Lucembursko'), (60, 'Madagaskar'),
(61, 'Malajsie'), (62, 'Maledivy'), (63, 'Malta'), (64, 'Marshallovy ostrovy'), (65, 'Mauricius'),
(66, 'Mexiko'), (67, 'Moldavsko'), (68, 'Monako'), (69, 'Mongolsko'), (70, 'Černá Hora'),
(71, 'Maroko'), (72, 'Nepál'), (73, 'Nizozemsko'), (74, 'Nový Zéland'), (75, 'Severní Makedonie'),
(76, 'Norsko'), (77, 'Omán'), (78, 'Panama'), (79, 'Peru'), (80, 'Filipíny'),
(81, 'Polsko'), (82, 'Portugalsko'), (83, 'Katar'), (84, 'Rumunsko'), (85, 'Rusko'),
(86, 'San Marino'), (87, 'Saúdská Arábie'), (88, 'Senegal'), (89, 'Srbsko'), (90, 'Seychely'),
(91, 'Singapur'), (92, 'Slovensko'), (93, 'Slovinsko'), (94, 'Jihoafrická republika'), (95, 'Španělsko'),
(96, 'Srí Lanka'), (97, 'Švédsko'), (98, 'Švýcarsko'), (99, 'Tanzanie'), (100, 'Thajsko'),
(101, 'Tunisko'), (102, 'Turecko'), (103, 'Ukrajina'), (104, 'Spojené království'), (105, 'Vietnam'),
(106, 'Spojené arabské emiráty');

-- Vložení destinací (měst/letovisek) pro jednotlivé státy
INSERT INTO destinace (id_statu, nazev_mesta) VALUES
(1, 'Tirana'), (1, 'Albánská riviéra'),
(2, 'St. John''s'), (2, 'English Harbour'),
(3, 'Buenos Aires'), (3, 'Patagonie'),
(4, 'Jerevan'), (4, 'Sjunik'),
(5, 'Sydney'), (5, 'Queensland'), (5, 'Severní teritorium'),
(6, 'Vídeň'), (6, 'Salcburk'), (6, 'Rakouské Alpy'),
(7, 'Nassau'), (7, 'Paradise Island'),
(8, 'Manáma'),
(9, 'Bridgetown'), (9, 'Carlisle Bay'),
(10, 'Minsk'), (10, 'Brest'),
(11, 'Brusel'), (11, 'Bruggy'),
(12, 'Cayo'), (12, 'Bariérový útes Belize'),
(13, 'Salar de Uyuni'), (13, 'La Paz'),
(14, 'Sarajevo'), (14, 'Mostar'),
(15, 'Delta Okavanga'), (15, 'Chobe'),
(16, 'Rio de Janeiro'), (16, 'Amazonie'),
(17, 'Sofie'), (17, 'Slunečné pobřeží'), (17, 'Plovdiv'),
(18, 'Sal'), (18, 'Boa Vista'),
(19, 'Toronto'), (19, 'Alberta'), (19, 'Ontario'),
(20, 'Grand Cayman'),
(21, 'Atacama'), (21, 'Patagonie'),
(22, 'Peking'), (22, 'Si-an'),
(23, 'Cartagena'), (23, 'Medellín'), (23, 'Tayrona'),
(24, 'Rarotonga'), (24, 'Aitutaki'),
(25, 'Puntarenas'), (25, 'Alajuela'),
(26, 'Dubrovník'), (26, 'Dalmácie'), (26, 'Lika-Senj'),
(27, 'Havana'), (27, 'Varadero'),
(28, 'Paphos'), (28, 'Ayia Napa'),
(29, 'Praha'), (29, 'Český Krumlov'),
(30, 'Kodaň'), (30, 'Billund'),
(31, 'Punta Cana'), (31, 'Santo Domingo'),
(32, 'Káhira'), (32, 'Luxor'), (32, 'Hurghada'),
(33, 'Tallinn'),
(34, 'Lalibela'), (34, 'Danakil'),
(35, 'Mamanuca'), (35, 'Yasawa'),
(36, 'Helsinky'), (36, 'Laponsko'),
(37, 'Paříž'), (37, 'Francouzská riviéra'), (37, 'Údolí Loiry'),
(38, 'Tbilisi'), (38, 'Kazbegi'),
(39, 'Berlín'), (39, 'Bavorsko'),
(40, 'Athény'), (40, 'Santorini'), (40, 'Kréta'),
(41, 'St. George''s'), (41, 'Grand Anse'),
(42, 'Budapešť'), (42, 'Balaton'),
(43, 'Reykjanes'), (43, 'Jižní Island'),
(44, 'Ágra'), (44, 'Nové Dillí'), (44, 'Rádžasthán'),
(45, 'Bali'), (45, 'Jáva'), (45, 'Jakarta'),
(46, 'Dublin'), (46, 'hrabství Clare'),
(47, 'Jeruzalém'), (47, 'Tel Aviv'),
(48, 'Řím'), (48, 'Benátky'), (48, 'Florencie'), (48, 'Amalfské pobřeží'),
(49, 'Montego Bay'), (49, 'Negril'),
(50, 'Tokio'), (50, 'Kjóto'),
(51, 'Petra'), (51, 'Wádí Rum'),
(52, 'Masai Mara'), (52, 'Mombasa'),
(53, 'Soul'), (53, 'Čedžu'),
(54, 'Kuwait City'),
(55, 'Riga'),
(56, 'Bejrút'), (56, 'Byblos'),
(57, 'Vaduz'),
(58, 'Vilnius'), (58, 'Trakai'),
(59, 'Lucemburk'),
(60, 'Menabe'), (60, 'Isalo'),
(61, 'Kuala Lumpur'), (61, 'Penang'), (61, 'Langkawi'),
(62, 'Malé'), (62, 'atoly Malediv'),
(63, 'Valletta'), (63, 'Comino'),
(64, 'Majuro'), (64, 'Bikini'),
(65, 'Le Morne'), (65, 'Chamarel'),
(66, 'Cancún'), (66, 'Riviéra Maya'), (66, 'Ciudad de México'), (66, 'Puerto Vallarta'),
(67, 'Kišiněv'), (67, 'Cricova'),
(68, 'Monte Carlo'), (68, 'Monaco-Ville'),
(69, 'Ulanbátar'), (69, 'Gobi'),
(70, 'Kotorský záliv'), (70, 'Budva'),
(71, 'Marrákeš'), (71, 'Fez'), (71, 'Chefchaouen'),
(72, 'Káthmándú'), (72, 'oblast Everestu'), (72, 'oblast Annapurny'),
(73, 'Amsterdam'), (73, 'Lisse'),
(74, 'Queenstown'), (74, 'Auckland'), (74, 'Fiordland'),
(75, 'Ohrid'), (75, 'Skopje'),
(76, 'Norské fjordy'), (76, 'Oslo'), (76, 'Lofoty'),
(77, 'Maskat'), (77, 'Nizwa'),
(78, 'Panama City'),
(79, 'Cusco'), (79, 'Posvátné údolí'),
(80, 'Boracay'), (80, 'Palawan'),
(81, 'Krakov'), (81, 'Varšava'), (81, 'Osvětim'),
(82, 'Lisabon'), (82, 'Porto'), (82, 'Algarve'),
(83, 'Dauhá'),
(84, 'Bukurešť'), (84, 'Transylvánie'),
(85, 'Moskva'), (85, 'Petrohrad'),
(86, 'San Marino'),
(87, 'Mekka'), (87, 'Medina'), (87, 'Rijád'), (87, 'Al Ula'),
(88, 'Dakar'), (88, 'Gorée'),
(89, 'Bělehrad'), (89, 'Novi Sad'),
(90, 'La Digue'), (90, 'Mahé'),
(91, 'Singapur'),
(92, 'Vysoké Tatry'), (92, 'Bratislava'),
(93, 'Bled'), (93, 'Lublaň'),
(94, 'Kapské Město'), (94, 'Mpumalanga'),
(95, 'Barcelona'), (95, 'Madrid'), (95, 'Andalusie'),
(96, 'Kandy'), (96, 'oblast Yala'),
(97, 'Stockholm'), (97, 'Laponsko'),
(98, 'Zermatt'), (98, 'oblast Jungfrau'), (98, 'Lucern'),
(99, 'Serengeti'), (99, 'Zanzibar'),
(100, 'Bangkok'), (100, 'Phuket'), (100, 'Chiang Mai'),
(101, 'Tunis'), (101, 'Djerba'),
(102, 'Istanbul'), (102, 'Kappadokie'),
(103, 'Kyjev'), (103, 'Lviv'),
(104, 'Londýn'), (104, 'Edinburgh'), (104, 'hrabství Wiltshire'),
(105, 'Hanoj'), (105, 'zátoka Ha Long'), (105, 'Hoi An'),
(106, 'Dubaj');

-- Aktualizace obrázků pro aktivní destinace
UPDATE `destinace` SET `obrazek` = '/images/kapverdy.jpg' WHERE `id_destinace` = 37;
UPDATE `destinace` SET `obrazek` = '/images/dubrovnik.jpg' WHERE `id_destinace` = 54;
UPDATE `destinace` SET `obrazek` = '/images/dalmacie.jpg' WHERE `id_destinace` = 55;
UPDATE `destinace` SET `obrazek` = '/images/egypt.jpg' WHERE `id_destinace` = 69;
UPDATE `destinace` SET `obrazek` = '/images/paris.jpg' WHERE `id_destinace` = 77;
UPDATE `destinace` SET `obrazek` = '/images/santorini.jpg' WHERE `id_destinace` = 85;
UPDATE `destinace` SET `obrazek` = '/images/kreta.jpg' WHERE `id_destinace` = 86;
UPDATE `destinace` SET `obrazek` = '/images/bali.jpg' WHERE `id_destinace` = 96;
UPDATE `destinace` SET `obrazek` = '/images/rome.jpg' WHERE `id_destinace` = 103;
UPDATE `destinace` SET `obrazek` = '/images/amalfi.jpg' WHERE `id_destinace` = 106;
UPDATE `destinace` SET `obrazek` = '/images/maledivy.jpg' WHERE `id_destinace` = 131;
UPDATE `destinace` SET `obrazek` = '/images/barcelona.jpg' WHERE `id_destinace` = 202;
UPDATE `destinace` SET `obrazek` = '/images/andalusie.jpg' WHERE `id_destinace` = 204;
UPDATE `destinace` SET `obrazek` = '/images/phuket.jpg' WHERE `id_destinace` = 215;
UPDATE `destinace` SET `obrazek` = '/images/london.jpg' WHERE `id_destinace` = 223;
