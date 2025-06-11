# Panoneau  

## Description

**Panoneau** est une application web conçue pour la **gestion des installations photovoltaïques chez les particuliers**.
Elle permet de **centraliser les données** relatives aux systèmes solaires, de **visualiser leur répartition géographique** et d'**obtenir des statistiques** sur leur déploiement en France.
L’application propose une interface publique pour la consultation des données et une interface administrateur pour la gestion complète des installations.


## Fonctionnalités

Le projet est structuré en deux parties distinctes :

- **Côté client** : lecture seule des données.
- **Côté administrateur** : gestion complète des données (ajout, modification, suppression, visualisation).

---

### Côté client

#### Page Accueil
Page principale du site.  
Elle affiche des statistiques générales sur les installations photovoltaïques, avec un en-tête contenant des boutons de navigation vers les autres pages.

#### Page Recherche
Affiche la liste de toutes les installations solaires connues.  
Chaque entrée présente la surface, la puissance, le nombre de panneaux, etc.  
Filtres disponibles : **département**, **marque de panneau**, **marque d'onduleur**.

Vous pouvez accéder à la **page Détail** d’une installation via le bouton « Détail ».

Système de pagination intégré :
- « 0 » : aller à la première page
- « < » / « > » : page précédente / suivante
- Champ pour entrer un numéro de page spécifique
- Champ pour définir le nombre d’installations affichées par page

#### Page Détail
Affiche toutes les informations détaillées d’un système photovoltaïque, organisées en sections pliables :
- **Installation** : informations générales
- **Placement** : méthode d’installation, angle, installateur
- **Adresse** : localisation précise
- **Panneau** : type et nombre de panneaux
- **Onduleur** : type et nombre d’onduleurs

#### Page Carte
Carte interactive affichant l’emplacement des installations.  
Filtres disponibles : **département** et **date d’installation**.

---

### Côté administrateur

#### Page de Connexion
Accès sécurisé à la partie administrateur via email et mot de passe.

#### Page Accueil (Admin)
Page principale de la partie « back office ».

Fonctionnalités :
- **Ajouter une nouvelle installation** en remplissant un formulaire.
- **Modifier** une installation existante.
- **Supprimer** une installation.
- **Consulter les détails** d'une installation comme sur le côté client.

---

## Comment utiliser

1. Accédez à l’URL de la machine virtuelle dans votre navigateur.
2. Vous arrivez sur la page d’accueil côté client.
3. Naviguez via les boutons du menu.
4. Pour accéder au côté admin, connectez-vous via la page de connexion avec les identifiants suivants : dupont@mail.com ; monMDP123!

---

### GitHub

Code source disponible ici : [https://github.com/Ange56/projet_photovoltaiques](https://github.com/Ange56/projet_photovoltaiques)
