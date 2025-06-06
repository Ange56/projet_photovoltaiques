-- Insertions pour la table Region
INSERT INTO Region (code, nom, pays) VALUES
(84, 'Auvergne-Rhone-Alpes', 'France'),
(76, 'Occitanie', 'France');

-- Insertions pour la table Departement
INSERT INTO Departement (code, nom, code_Region) VALUES
('1', 'Ain', 84),
('31', 'Haute-Garonne', 76),
('34', 'Herault', 76),
('26', 'Drome', 84);

-- Insertions pour la table Communes
INSERT INTO Communes (code_insee, nom_standard, code_postal, code_postal_suffix, population, code) VALUES
(1001, 'L Abergement-Clemenciat', 1400, NULL, 806, '1'),
(1002, 'L Abergement-de-Varey', 1640, NULL, 262, '1'),
(1004, 'Amberieu-en-Bugey', 1500, NULL, 14288, '1'),
(1005, 'Test de ville', 1800, NULL, 5689, '34');

-- Insertions pour la table Marque_panneau
INSERT INTO Marque_panneau (nom) VALUES
('Sanyo'),
('Canadian Solar'),
('Mitsubishi'),
('Schwaben_Solar'),
('SUNOLOGY'),
('Sunrise'),
('ECO_DELTA'),
('FATH_Solar'),
('ORI_Solar');

-- Insertions pour la table Modele_panneau
INSERT INTO Modele_panneau (nom_modele) VALUES
('HIP-215 NKHE1'),
('CS5A-170M'),
('PV-MF175 TD4');

-- Insertions pour la table Marque_onduleur
INSERT INTO Marque_onduleur (nom) VALUES
('SMA'),
('Mastervolt');

-- Insertions pour la table Modele_onduleur
INSERT INTO Modele_onduleur (nom) VALUES
('Sunny Boy 2800'),
('SunMaster QS 3500'),
('Sunny Boy 2500');

-- Insertions pour la table Onduleur
INSERT INTO Onduleur (id_onduleur, nom, nom_Marque_onduleur) VALUES
(1, 'Sunny Boy 2800', 'SMA'),
(2, 'SunMaster QS 3500', 'Mastervolt'),
(3, 'Sunny Boy 2500', 'SMA');

-- Insertions pour la table Installateur
INSERT INTO Installateur (id, nom) VALUES
(1, 'MECOTECH'),
(2, 'Helio Therma'),
(3, 'Cervin Enr'),
(4, 'SolEco'),
(5, 'GreenVolt');

-- Insertions pour la table Panneau
INSERT INTO Panneau (id_panneau, nom_modele, nom) VALUES
(1, 'HIP-215 NKHE1', 'Sanyo'),
(2, 'CS5A-170M', 'Canadian Solar'),
(3, 'PV-MF175 TD4', 'Mitsubishi');

-- Insertions pour la table Installation
-- Nouvelles insertions pour la table Installation
INSERT INTO Installation (
    id, iddoc, an_installation, mois_installation, nb_panneaux,
    lat, `long`, production_pvgis, puissance_crete,
    pente, pente_optimum, surface, orientation, orientation_optimum,
    administrative_area_level3, administrative_area_level4, political,
    id_Installateur, id_onduleur, code_insee, id_panneau
) VALUES
(26, 26, 2024, 3, 24, 45.78321, 4.83201, 7892, 4320, 10, 35, 50, 90, 0, NULL, NULL, NULL, 1, 2, 1001, 2),
(27, 27, 2020, 6, 38, 44.12345, 1.98765, 11500, 8432, 20, 40, 70, -90, -45, NULL, NULL, NULL, 2, 1, 1002, 1),
(28, 28, 2018, 9, 41, 43.78923, 3.45678, 9721, 6523, 12, 30, 60, 45, -90, NULL, NULL, NULL, 3, 3, 1005, 3),
(29, 29, 2017, 12, 22, 46.34567, 2.12345, 8320, 5342, 15, 35, 45, -45, 90, NULL, NULL, NULL, 4, 2, 1004, 2),
(30, 30, 2019, 7, 33, 47.12389, 5.87654, 10760, 7021, 25, 32, 88, 0, -90, NULL, NULL, NULL, 5, 3, 1004, 1),
(31, 31, 2021, 10, 29, 43.45678, 2.34567, 9988, 6145, 18, 28, 54, 90, 45, NULL, NULL, NULL, 1, 1, 1002, 3),
(32, 32, 2015, 1, 36, 45.98765, 1.87654, 8990, 6890, 8, 30, 59, -90, 0, NULL, NULL, NULL, 3, 2, 1005, 2),
(33, 33, 2022, 5, 57, 44.67890, 3.21098, 13450, 7450, 13, 29, 102, 0, 90, NULL, NULL, NULL, 4, 1, 1001, 1),
(34, 34, 2016, 2, 18, 46.54321, 0.98765, 7450, 4800, 9, 25, 36, 45, -90, NULL, NULL, NULL, 5, 2, 1004, 2),
(35, 35, 2023, 8, 44, 43.11111, 3.44444, 12000, 8800, 11, 33, 93, 90, -45, NULL, NULL, NULL, 2, 3, 1005, 1);

