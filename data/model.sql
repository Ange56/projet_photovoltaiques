#------------------------------------------------------------
# Table: Region
#------------------------------------------------------------

CREATE TABLE Region(
    code Int NOT NULL,
    nom Varchar(50) NOT NULL,
    pays Varchar(50) NOT NULL,
    CONSTRAINT Region_PK PRIMARY KEY (code)
);


#------------------------------------------------------------
# Table: Departement
#------------------------------------------------------------

CREATE TABLE Departement(
    code Varchar(3) NOT NULL,
    nom Varchar(50) NOT NULL,
    code_Region Int NOT NULL,
    CONSTRAINT Departement_PK PRIMARY KEY (code),
    CONSTRAINT Departement_Region_FK FOREIGN KEY (code_Region) REFERENCES Region(code)
);


#------------------------------------------------------------
# Table: Modele_panneau
#------------------------------------------------------------

CREATE TABLE Modele_panneau(
    nom_modele Varchar(50) NOT NULL,
    CONSTRAINT Modele_panneau_PK PRIMARY KEY (nom_modele)
);


#------------------------------------------------------------
# Table: Marque_panneau
#------------------------------------------------------------

CREATE TABLE Marque_panneau(
    nom Varchar(50) NOT NULL,
    CONSTRAINT Marque_panneau_PK PRIMARY KEY (nom)
);


#------------------------------------------------------------
# Table: Installateur
#------------------------------------------------------------

CREATE TABLE Installateur(
    id Int AUTO_INCREMENT NOT NULL,
    nom Varchar(50) NOT NULL,
    CONSTRAINT Installateur_PK PRIMARY KEY (id)
);


#------------------------------------------------------------
# Table: Communes
#------------------------------------------------------------

CREATE TABLE Communes(
    code_insee VARCHAR(10) NOT NULL,
    nom_standard Varchar(50) NOT NULL,
    code_postal Int NOT NULL,
    code_postal_suffix Int,
    population Int NOT NULL,
    code Varchar(3) NOT NULL,
    CONSTRAINT Communes_PK PRIMARY KEY (code_insee),
    CONSTRAINT Communes_Departement_FK FOREIGN KEY (code) REFERENCES Departement(code)
);


#------------------------------------------------------------
# Table: Modele_onduleur
#------------------------------------------------------------

CREATE TABLE Modele_onduleur(
    nom Varchar(50) NOT NULL,
    CONSTRAINT Modele_onduleur_PK PRIMARY KEY (nom)
);


#------------------------------------------------------------
# Table: Marque_onduleur
#------------------------------------------------------------

CREATE TABLE Marque_onduleur(
    nom Varchar(50) NOT NULL,
    CONSTRAINT Marque_onduleur_PK PRIMARY KEY (nom)
);


#------------------------------------------------------------
# Table: Onduleur
#------------------------------------------------------------

CREATE TABLE Onduleur(
    id_onduleur Int AUTO_INCREMENT NOT NULL,
    nom Varchar(50) NOT NULL,
    nom_Marque_onduleur Varchar(50) NOT NULL,
    CONSTRAINT Onduleur_PK PRIMARY KEY (id_onduleur),
    CONSTRAINT Onduleur_Modele_onduleur_FK FOREIGN KEY (nom) REFERENCES Modele_onduleur(nom),
    CONSTRAINT Onduleur_Marque_onduleur0_FK FOREIGN KEY (nom_Marque_onduleur) REFERENCES Marque_onduleur(nom)
);


#------------------------------------------------------------
# Table: Panneau
#------------------------------------------------------------

CREATE TABLE Panneau(
    id_panneau Int AUTO_INCREMENT NOT NULL,
    nom_modele Varchar(50) NOT NULL,
    nom Varchar(50) NOT NULL,
    CONSTRAINT Panneau_PK PRIMARY KEY (id_panneau),
    CONSTRAINT Panneau_Modele_panneau_FK FOREIGN KEY (nom_modele) REFERENCES Modele_panneau(nom_modele),
    CONSTRAINT Panneau_Marque_panneau0_FK FOREIGN KEY (nom) REFERENCES Marque_panneau(nom)
);


#------------------------------------------------------------
# Table: Installation
#------------------------------------------------------------

CREATE TABLE Installation(
    id Int AUTO_INCREMENT NOT NULL,
    iddoc Int NOT NULL,
    an_installation Int NOT NULL,
    mois_installation Int NOT NULL,
    nb_panneaux Int NOT NULL,
    lat Float,
    `long` Float,
    production_pvgis Int,
    puissance_crete Int NOT NULL,
    pente Int NOT NULL,
    pente_optimum Int,
    surface Int NOT NULL,
    orientation Int NOT NULL,
    orientation_optimum Int,
    administrative_area_level3 Varchar(50),
    administrative_area_level4 Varchar(50),
    political Varchar(50),
    id_Installateur Int,
    id_onduleur Int NOT NULL,
    code_insee VARCHAR(10) NOT NULL,
    id_panneau Int NOT NULL,
    CONSTRAINT Installation_PK PRIMARY KEY (id),
    CONSTRAINT Installation_Installateur_FK FOREIGN KEY (id_Installateur) REFERENCES Installateur(id),
    CONSTRAINT Installation_Onduleur0_FK FOREIGN KEY (id_onduleur) REFERENCES Onduleur(id_onduleur),
    CONSTRAINT Installation_Communes1_FK FOREIGN KEY (code_insee) REFERENCES Communes(code_insee),
    CONSTRAINT Installation_Panneau2_FK FOREIGN KEY (id_panneau) REFERENCES Panneau(id_panneau)
);
