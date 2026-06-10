-- Vyprázdnění tabulky letišť, aby se předešlo duplicitám (podobně jako u destinací)
SET FOREIGN_KEY_CHECKS = 0;
DELETE FROM letiste;
ALTER TABLE letiste AUTO_INCREMENT = 1;
SET FOREIGN_KEY_CHECKS = 1;

-- Vložení letišť s vazbou na ID států (Česko = 29, Slovensko = 92, Rakousko = 6, Polsko = 81, Maďarsko = 42, Německo = 39)
-- (Poznámka: IATA kódy zadávám prázdné nebo podle skutečnosti, záleží na struktuře tvé databáze. Předpokládám sloupce id_statu a mesto)

INSERT INTO letiste (iata, id_statu, mesto) VALUES
-- Česká republika (ID 29)
('PRG', 29, 'Praha'),
('BRQ', 29, 'Brno'),
('OSR', 29, 'Ostrava'),
('PED', 29, 'Pardubice'),
('JCL', 29, 'České Budějovice'),
('KLV', 29, 'Karlovy Vary'),

-- Slovensko (ID 92)
('BTS', 92, 'Bratislava'),
('KSC', 92, 'Košice'),
('TAT', 92, 'Poprad/Tatry'),

-- Rakousko (ID 6)
('LNZ', 6, 'Linz'),
('SZG', 6, 'Salzburg'),
('VIE', 6, 'Vídeň'),

-- Polsko (ID 81)
('KTW', 81, 'Katowice'),
('KRK', 81, 'Krakow'),
('WAW', 81, 'Varšava - F. Chopin'),

-- Maďarsko (ID 42)
('BUD', 42, 'Budapešť'),

-- Německo (ID 39)
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
