-- Insertions pour la table Region
INSERT INTO Region (code, nom, pays) VALUES
(84, 'Auvergne-Rhône-Alpes', 'France'),
(76, 'Occitanie', 'France');

-- Insertions pour la table Departement
INSERT INTO Departement (code, nom, code_Region) VALUES
('1', 'Ain', 84),
('31', 'Haute-Garonne', 76),
('34', 'Hérault', 76),
('26', 'Drôme', 84);

-- Insertions pour la table Communes
INSERT INTO Communes (code_insee, nom_standard, code_postal, code_postal_suffix, population, code) VALUES
(1001, "L'Abergement-Clémenciat", 1400, NULL, 806, '1'),
(1002, "L'Abergement-de-Varey", 1640, NULL, 262, '1'),
(1004, "Ambérieu-en-Bugey", 1500, NULL, 14288, '1');

-- Insertions pour la table Marque_panneau
INSERT INTO Marque_panneau (nom) VALUES
('Sanyo'),
('Canadian Solar'),
('Mitsubishi');

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
(3, 'Cervin Enr');

-- Insertions pour la table Panneau
INSERT INTO Panneau (id_panneau, nom_modele, nom) VALUES
(1, 'HIP-215 NKHE1', 'Sanyo'),
(2, 'CS5A-170M', 'Canadian Solar'),
(3, 'PV-MF175 TD4', 'Mitsubishi');

-- Insertions pour la table Installation
INSERT INTO Installation (
    id, iddoc, an_installation, mois_installation, nb_panneaux,
    installateur, lat, `long`, production_pvgis, puissance_crete,
    pente, pente_optimum, surface, orientation, orientation_optimum,
    administrative_area_level3, administrative_area_level4, political,
    id_Installateur, id_onduleur, code_insee, id_panneau
) VALUES
(1, 1, '2007-01-01', '2007-09-01', 14, 'MECOTECH', 43.51, 1.51, 3633, 3010, 20, 37, 22, -20, 1, NULL, NULL, NULL, 1, 1, 1001, 1),
(2, 4, '2008-01-01', '2008-03-01', 18, 'Helio Therma', 43.5, 3.37, 3742, 3060, 20, 38, 23, 70, 0, NULL, NULL, NULL, 2, 2, 1002, 2),
(3, 12, '2007-01-01', '2007-12-01', 63, 'Cervin Enr', 45.06, 4.88, 13360, 11025, 15, 37, 81, -20, 0, NULL, NULL, NULL, 3, 3, 1004, 3);


INSERT INTO Personne (mdp, email, nom, prenom) VALUES 
('monMDP123!', 'dupont@mail.com', 'Dupont', 'Jean'),
('monMDP456!', 'dujardins@mail.com', 'Dujardins', 'Charlotte'),
('monMDP789!', 'orlande@mail.com', 'Orlande', 'Charles');

