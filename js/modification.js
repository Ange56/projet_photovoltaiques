// Variables globales
let installationData = null;
let installationId = null;

// Fonction d'initialisation au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Récupérer l'ID de l'installation depuis l'URL
    const urlParams = new URLSearchParams(window.location.search);
    installationId = urlParams.get('id');
    
    if (!installationId) {
        alert('Aucun ID d\'installation fourni');
        window.location.href = 'accueil.php';
        return;
    }
    
    // Charger les données de l'installation
    chargerInstallation();
    
    // Initialiser les événements des boutons de section
    initializeSectionToggles();
});

// Fonction pour charger les données de l'installation
async function chargerInstallation() {
    try {
        console.log('Chargement de l\'installation ID:', installationId);
        const response = await fetch(`../api/endpoints/get.php?action=installation_detail&id=${installationId}`);
        
        if (!response.ok) {
            throw new Error(`Erreur HTTP: ${response.status}`);
        }
        
        const responseText = await response.text();
        console.log('Réponse brute du serveur:', responseText);
        
        try {
            installationData = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Erreur de parsing JSON:', parseError);
            throw new Error('Réponse serveur invalide');
        }
        
        if (!installationData) {
            throw new Error('Installation non trouvée');
        }
        
        console.log('Données de l\'installation chargées:', installationData);
        
        // Mettre à jour les titres
        document.getElementById('info').textContent = `Modification de l'installation: ${installationData.nom_standard || 'Installation #' + installationId}`;
        document.getElementById('infodoc').textContent = `ID du document: ${installationData.iddoc || installationId}`;
        
        // Construire les tableaux de données
        construireTableaux();
        
    } catch (error) {
        console.error('Erreur lors du chargement:', error);
        alert('Erreur lors du chargement de l\'installation: ' + error.message);
    }
}

// Fonction pour construire tous les tableaux
function construireTableaux() {
    construireTableauInstallation();
    construireTableauPlacement();
    construireTableauAdresse();
    construireTableauPanneau();
    construireTableauOnduleur();
    construireTableauInstallateur(); 

    
    // Ajouter le bouton de sauvegarde global à la fin
    ajouterBoutonSauvegardeGlobal();
}

// Construction du tableau Installation
function construireTableauInstallation() {
    const table = document.getElementById('installationm');
    const dateInstallation = formatDate(installationData.an_installation, installationData.mois_installation);
    
    table.innerHTML = `
        <tr>
            <td><strong>Date d'installation:</strong></td>
            <td>
                <input type="month" id="date_installation" value="${dateInstallation}" class="form-control">
            </td>
        </tr>
        <tr>
            <td><strong>Nombre de panneaux:</strong></td>
            <td>
                <input type="number" id="nb_panneaux" value="${installationData.nb_panneaux || ''}" class="form-control" min="1">
            </td>
        </tr>
        <tr>
            <td><strong>Surface (m²):</strong></td>
            <td>
                <input type="number" id="surface" value="${installationData.surface || ''}" class="form-control" min="0" step="0.1">
            </td>
        </tr>
        <tr>
            <td><strong>Puissance crête (kWc):</strong></td>
            <td>
                <input type="number" id="puissance_crete" value="${installationData.puissance_crete || ''}" class="form-control" min="0" step="0.1">
            </td>
        </tr>
        <tr>
            <td><strong>Production PVGIS (kWh/an):</strong></td>
            <td>
                <input type="number" id="production_pvgis" value="${installationData.production_pvgis || ''}" class="form-control" min="0" readonly>
            </td>
        </tr>
    `;
}

// Construction du tableau Placement
function construireTableauPlacement() {
    const table = document.getElementById('placementm');
    
    table.innerHTML = `
        <tr>
            <td><strong>Orientation (°):</strong></td>
            <td>
                <input type="number" id="orientation" value="${installationData.orientation || ''}" class="form-control" min="0" max="360">
                <small class="text-muted">0° = Nord, 90° = Est, 180° = Sud, 270° = Ouest</small>
            </td>
        </tr>
        <tr>
            <td><strong>Orientation optimum (°):</strong></td>
            <td>
                <input type="number" id="orientation_optimum" value="${installationData.orientation_optimum || ''}" class="form-control" readonly>
            </td>
        </tr>
        <tr>
            <td><strong>Pente (°):</strong></td>
            <td>
                <input type="number" id="pente" value="${installationData.pente || ''}" class="form-control" min="0" max="90">
            </td>
        </tr>
        <tr>
            <td><strong>Pente optimum (°):</strong></td>
            <td>
                <input type="number" id="pente_optimum" value="${installationData.pente_optimum || ''}" class="form-control" readonly>
            </td>
        </tr>
    `;
}

// Construction du tableau Adresse
function construireTableauAdresse() {
    const table = document.getElementById('adressem');
    
    table.innerHTML = `
        <tr>
            <td><strong>Localisation:</strong></td>
            <td>
                <input type="text" id="localisation" value="${installationData.nom_standard || ''}" class="form-control" list="communes-list">
                <datalist id="communes-list"></datalist>
            </td>
        </tr>
        <tr>
            <td><strong>Code postal:</strong></td>
            <td>
                <input type="text" id="code_postal" value="${installationData.code_postal || ''}" class="form-control" pattern="[0-9]{5}">
            </td>
        </tr>
        <tr>
            <td><strong>Département:</strong></td>
            <td>
                <select id="departement" class="form-control">
                    <option value="">Sélectionner un département</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>Région:</strong></td>
            <td>
                <select id="region" class="form-control">
                    <option value="">Sélectionner une région</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>Latitude:</strong></td>
            <td>
                <input type="number" id="latitude" value="${installationData.lat || ''}" class="form-control" step="0.000001">
            </td>
        </tr>
        <tr>
            <td><strong>Longitude:</strong></td>
            <td>
                <input type="number" id="longitude" value="${installationData.long || ''}" class="form-control" step="0.000001">
            </td>
        </tr>
    `;
    
    // Charger les listes
    chargerCommunes();
    chargerDepartements();
    chargerRegions();
}

// Construction du tableau Panneau
function construireTableauPanneau() {
    const table = document.getElementById('panneaum');
    
    table.innerHTML = `
        <tr>
            <td><strong>ID Panneau:</strong></td>
            <td>
                <input type="text" value="${installationData.id_panneau || ''}" class="form-control" readonly>
            </td>
        </tr>
        <tr>
            <td><strong>Marque:</strong></td>
            <td>
                <select id="marque_panneau" class="form-control">
                    <option value="">Sélectionner une marque</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>Modèle:</strong></td>
            <td>
                <select id="modele_panneau" class="form-control">
                    <option value="">Sélectionner un modèle</option>
                </select>
            </td>
        </tr>
    `;
    
    // Charger les marques et modèles de panneaux
    chargerMarquesPanneaux();
    chargerModelesPanneaux();
}

// Construction du tableau Onduleur
function construireTableauOnduleur() {
    const table = document.getElementById('onduleurm');
    
    table.innerHTML = `
        <tr>
            <td><strong>ID Onduleur:</strong></td>
            <td>
                <input type="text" value="${installationData.id_onduleur || ''}" class="form-control" readonly>
            </td>
        </tr>
        <tr>
            <td><strong>Marque:</strong></td>
            <td>
                <select id="marque_onduleur" class="form-control">
                    <option value="">Sélectionner une marque</option>
                </select>
            </td>
        </tr>
        <tr>
            <td><strong>Modèle:</strong></td>
            <td>
                <select id="modele_onduleur" class="form-control">
                    <option value="">Sélectionner un modèle</option>
                </select>
            </td>
        </tr>
    `;
    
    // Charger les marques et modèles d'onduleurs
    chargerMarquesOnduleurs();
    chargerModelesOnduleurs();
}


function construireTableauInstallateur() {
    const table = document.getElementById('installateurm');
    
    if (!table) {
        console.warn('Tableau installateur non trouvé dans le HTML');
        return;
    }
    
    table.innerHTML = `
        <tr>
            <td><strong>Nom de l'installateur:</strong></td>
            <td>
                <select id="nom_installateur" class="form-control">
                    <option value="">Sélectionner un installateur</option>
                </select>
            </td>
        </tr>
    `;
    
    // Charger la liste des installateurs
    chargerInstallateurs();
}

// Fonction pour ajouter le bouton de sauvegarde global
function ajouterBoutonSauvegardeGlobal() {
    const main = document.getElementById('page');
    
    // Vérifier si le bouton existe déjà
    if (document.getElementById('btn-sauvegarder-global')) {
        return;
    }
    
    const divBouton = document.createElement('div');
    divBouton.className = 'text-center mt-4 mb-4';
    divBouton.innerHTML = `
        <button type="button" id="btn-sauvegarder-global" class="btn btn-success btn-lg me-3">
            <i class="fas fa-save me-2"></i>Sauvegarder toutes les modifications
        </button>
        <button type="button" id="btn-retour-accueil" class="btn btn-secondary btn-lg">
            <i class="fas fa-arrow-left me-2"></i>Retour à l'accueil
        </button>
    `;
    
    main.appendChild(divBouton);
    
    // Ajouter les événements de clic
    document.getElementById('btn-sauvegarder-global').addEventListener('click', sauvegarderToutesModifications);
    document.getElementById('btn-retour-accueil').addEventListener('click', function() {
        window.location.href = 'accueil.php';
    });
}

// Fonction pour initialiser les événements des boutons de section
function initializeSectionToggles() {
    const buttons = ['installationb', 'placementb', 'adresseb', 'panneaub', 'onduleurb'];
    const tables = ['installationm', 'placementm', 'adressem', 'panneaum', 'onduleurm'];
    
    buttons.forEach((buttonId, index) => {
        const button = document.getElementById(buttonId);
        const table = document.getElementById(tables[index]);
        
        if (button && table) {
            // Afficher initialement toutes les tables
            table.style.display = 'table';
            
            button.addEventListener('click', function() {
                toggleTable(table);
            });
        }
    });
}

// Fonction pour afficher/masquer les tableaux
function toggleTable(table) {
    if (table.style.display === 'none' || table.style.display === '') {
        table.style.display = 'table';
    } else {
        table.style.display = 'none';
    }
}

// Fonction pour charger la liste des communes
async function chargerCommunes() {
    try {
        const response = await fetch('../api/endpoints/get.php?action=communes_list');
        
        if (!response.ok) {
            console.warn('Impossible de charger la liste des communes');
            return;
        }
        
        const data = await response.json();
        const datalist = document.getElementById('communes-list');
        
        if (data.communes && datalist) {
            data.communes.forEach(commune => {
                const option = document.createElement('option');
                option.value = commune.nom_standard;
                datalist.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des communes:', error);
    }
}

// Fonction pour charger la liste des départements
// async function chargerDepartements() {
//     try {
//         const response = await fetch('../api/endpoints/get.php?action=departements_list');
        
//         if (!response.ok) {
//             console.warn('Impossible de charger la liste des départements');
//             return;
//         }
        
//         const data = await response.json();
//         const select = document.getElementById('departement');
        
//         if (data.departements && select) {
//             data.departements.forEach(dept => {
//                 const option = document.createElement('option');
//                 option.value = dept.code;
//                 option.textContent = `${dept.code} - ${dept.nom}`;
//                 if (dept.code === installationData.code_departement) {
//                     option.selected = true;
//                 }
//                 select.appendChild(option);
//             });
//         }
//     } catch (error) {
//         console.error('Erreur lors du chargement des départements:', error);
//     }
// }
async function chargerDepartements() {
    try {
        // Correction: utiliser l'endpoint correct 'departements_random'
        const response = await fetch('../api/endpoints/get.php?action=departements_random');
        
        if (!response.ok) {
            console.warn('Impossible de charger la liste des départements');
            return;
        }
        
        const data = await response.json();
        const select = document.getElementById('departement');
        
        if (data.departements && select) {
            data.departements.forEach(dept => {
                const option = document.createElement('option');
                option.value = dept.code;
                option.textContent = ` ${dept.nom}`;
                if (dept.code === installationData.code_departement) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des départements:', error);
    }
}

// Fonction pour charger la liste des régions
// async function chargerRegions() {
//     try {
//         const response = await fetch('../api/endpoints/get.php?action=regions_list');
        
//         if (!response.ok) {
//             console.warn('Impossible de charger la liste des régions');
//             return;
//         }
        
//         const data = await response.json();
//         const select = document.getElementById('region');
        
//         if (data.regions && select) {
//             data.regions.forEach(region => {
//                 const option = document.createElement('option');
//                 option.value = region.code;
//                 option.textContent = region.nom;
//                 if (region.code === installationData.code_region) {
//                     option.selected = true;
//                 }
//                 select.appendChild(option);
//             });
//         }
//     } catch (error) {
//         console.error('Erreur lors du chargement des régions:', error);
//     }
// }
async function chargerRegions() {
    try {
        // Pour les régions, on doit ajouter l'endpoint dans get.php
        const response = await fetch('../api/endpoints/get.php?action=regions_list');
        
        if (!response.ok) {
            console.warn('Impossible de charger la liste des régions');
            return;
        }
        
        const data = await response.json();
        const select = document.getElementById('region');
        
        if (data.regions && select) {
            data.regions.forEach(region => {
                const option = document.createElement('option');
                option.value = region.code;
                option.textContent = region.nom;
                if (region.code === installationData.code_region) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des régions:', error);
    }
}

// Fonction pour charger les marques de panneaux
// async function chargerMarquesPanneaux() {
//     try {
//         const response = await fetch('../api/endpoints/get.php?action=marques_panneaux');
        
//         if (!response.ok) {
//             console.warn('Impossible de charger les marques de panneaux');
//             return;
//         }
        
//         const data = await response.json();
//         const select = document.getElementById('marque_panneau');
        
//         if (data.marques && select) {
//             data.marques.forEach(marque => {
//                 const option = document.createElement('option');
//                 option.value = marque.nom;
//                 option.textContent = marque.nom;
//                 if (marque.nom === installationData.nom_panneau) {
//                     option.selected = true;
//                 }
//                 select.appendChild(option);
//             });
//         }
//     } catch (error) {
//         console.error('Erreur lors du chargement des marques de panneaux:', error);
//     }
// }
async function chargerMarquesPanneaux() {
    try {
        // Correction: utiliser l'endpoint correct 'marques_panneaux'
        const response = await fetch('../api/endpoints/get.php?action=marques_panneaux');
        
        if (!response.ok) {
            console.warn('Impossible de charger les marques de panneaux');
            return;
        }
        
        const data = await response.json();
        const select = document.getElementById('marque_panneau');
        
        // Correction: accéder à la bonne propriété
        if (data.marques_panneaux && select) {
            data.marques_panneaux.forEach(marque => {
                const option = document.createElement('option');
                option.value = marque.nom;
                option.textContent = marque.nom;
                if (marque.nom === installationData.nom_panneau) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des marques de panneaux:', error);
    }
}

// Fonction pour charger les modèles de panneaux
// async function chargerModelesPanneaux() {
//     try {
//         const response = await fetch('../api/endpoints/get.php?action=modeles_panneaux');
        
//         if (!response.ok) {
//             console.warn('Impossible de charger les modèles de panneaux');
//             return;
//         }
        
//         const data = await response.json();
//         const select = document.getElementById('modele_panneau');
        
//         if (data.modeles && select) {
//             data.modeles.forEach(modele => {
//                 const option = document.createElement('option');
//                 option.value = modele.nom_modele;
//                 option.textContent = modele.nom_modele;
//                 if (modele.nom_modele === installationData.nom_modele) {
//                     option.selected = true;
//                 }
//                 select.appendChild(option);
//             });
//         }
//     } catch (error) {
//         console.error('Erreur lors du chargement des modèles de panneaux:', error);
//     }
// }
async function chargerModelesPanneaux() {
    try {
        // Il faut ajouter cet endpoint dans get.php
        const response = await fetch('../api/endpoints/get.php?action=modeles_panneaux');
        
        if (!response.ok) {
            console.warn('Impossible de charger les modèles de panneaux');
            return;
        }
        
        const data = await response.json();
        const select = document.getElementById('modele_panneau');
        
        if (data.modeles && select) {
            data.modeles.forEach(modele => {
                const option = document.createElement('option');
                option.value = modele.nom_modele;
                option.textContent = modele.nom_modele;
                if (modele.nom_modele === installationData.nom_modele) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des modèles de panneaux:', error);
    }
}

// Fonction pour charger les marques d'onduleurs
// async function chargerMarquesOnduleurs() {
//     try {
//         const response = await fetch('../api/endpoints/get.php?action=marques_onduleurs');
        
//         if (!response.ok) {
//             console.warn('Impossible de charger les marques d\'onduleurs');
//             return;
//         }
        
//         const data = await response.json();
//         const select = document.getElementById('marque_onduleur');
        
//         if (data.marques && select) {
//             data.marques.forEach(marque => {
//                 const option = document.createElement('option');
//                 option.value = marque.nom;
//                 option.textContent = marque.nom;
//                 if (marque.nom === installationData.nom_Marque_onduleur) {
//                     option.selected = true;
//                 }
//                 select.appendChild(option);
//             });
//         }
//     } catch (error) {
//         console.error('Erreur lors du chargement des marques d\'onduleurs:', error);
//     }
// }
async function chargerMarquesOnduleurs() {
    try {
        // Correction: utiliser l'endpoint correct 'marques_onduleurs'
        const response = await fetch('../api/endpoints/get.php?action=marques_onduleurs');
        
        if (!response.ok) {
            console.warn('Impossible de charger les marques d\'onduleurs');
            return;
        }
        
        const data = await response.json();
        const select = document.getElementById('marque_onduleur');
        
        // Correction: accéder à la bonne propriété
        if (data.marques_onduleurs && select) {
            data.marques_onduleurs.forEach(marque => {
                const option = document.createElement('option');
                option.value = marque.nom;
                option.textContent = marque.nom;
                if (marque.nom === installationData.nom_Marque_onduleur) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des marques d\'onduleurs:', error);
    }
}

// // Fonction pour charger les modèles d'onduleurs
// async function chargerModelesOnduleurs() {
//     try {
//         const response = await fetch('../api/endpoints/get.php?action=modeles_onduleurs');
        
//         if (!response.ok) {
//             console.warn('Impossible de charger les modèles d\'onduleurs');
//             return;
//         }
        
//         const data = await response.json();
//         const select = document.getElementById('modele_onduleur');
        
//         if (data.modeles && select) {
//             data.modeles.forEach(modele => {
//                 const option = document.createElement('option');
//                 option.value = modele.nom;
//                 option.textContent = modele.nom;
//                 if (modele.nom === installationData.nom_onduleur) {
//                     option.selected = true;
//                 }
//                 select.appendChild(option);
//             });
//         }
//     } catch (error) {
//         console.error('Erreur lors du chargement des modèles d\'onduleurs:', error);
//     }
// }
async function chargerModelesOnduleurs() {
    try {
        // Il faut ajouter cet endpoint dans get.php
        const response = await fetch('../api/endpoints/get.php?action=modeles_onduleurs');
        
        if (!response.ok) {
            console.warn('Impossible de charger les modèles d\'onduleurs');
            return;
        }
        
        const data = await response.json();
        const select = document.getElementById('modele_onduleur');
        
        if (data.modeles && select) {
            data.modeles.forEach(modele => {
                const option = document.createElement('option');
                option.value = modele.nom;
                option.textContent = modele.nom;
                if (modele.nom === installationData.nom_onduleur) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des modèles d\'onduleurs:', error);
    }
}

async function chargerInstallateurs() {
    try {
        const response = await fetch('../api/endpoints/get.php?action=installateurs_list');
        
        if (!response.ok) {
            console.warn('Impossible de charger la liste des installateurs');
            return;
        }
        
        const data = await response.json();
        const select = document.getElementById('nom_installateur');
        
        if (data.installateurs && select) {
            data.installateurs.forEach(installateur => {
                const option = document.createElement('option');
                option.value = installateur.id_installateur;
                option.textContent = installateur.nom;
                if (installateur.id_installateur == installationData.id_installateur) {
                    option.selected = true;
                }
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Erreur lors du chargement des installateurs:', error);
    }
}
// Fonction pour formater la date
function formatDate(annee, mois) {
    if (!annee || !mois) return '';
    const moisFormate = mois.toString().padStart(2, '0');
    return `${annee}-${moisFormate}`;
}

// Fonction pour récupérer toutes les valeurs du formulaire
function recupererValeursFormulaire() {
    return {
        // Installation
        date_installation: document.getElementById('date_installation')?.value || '',
        nb_panneaux: document.getElementById('nb_panneaux')?.value || '',
        surface: document.getElementById('surface')?.value || '',
        puissance_crete: document.getElementById('puissance_crete')?.value || '',
        
        // Placement
        orientation: document.getElementById('orientation')?.value || '',
        pente: document.getElementById('pente')?.value || '',
        
        // Adresse
        localisation: document.getElementById('localisation')?.value || '',
        code_postal: document.getElementById('code_postal')?.value || '',
        departement: document.getElementById('departement')?.value || '',
        region: document.getElementById('region')?.value || '',
        latitude: document.getElementById('latitude')?.value || '',
        longitude: document.getElementById('longitude')?.value || '',
        
        // Panneau
        marque_panneau: document.getElementById('marque_panneau')?.value || '',
        modele_panneau: document.getElementById('modele_panneau')?.value || '',
        
        // Onduleur
        marque_onduleur: document.getElementById('marque_onduleur')?.value || '',
        modele_onduleur: document.getElementById('modele_onduleur')?.value || '',
    
        //installateur
        nom_installateur: document.getElementById('nom_installateur')?.value || ''

    };
}

// Fonction pour valider les données
function validerDonnees(donnees) {
    const erreurs = [];
    
    // Champs obligatoires
    if (!donnees.date_installation) erreurs.push('Date d\'installation');
    if (!donnees.nb_panneaux) erreurs.push('Nombre de panneaux');
    if (!donnees.surface) erreurs.push('Surface');
    if (!donnees.puissance_crete) erreurs.push('Puissance crête');
    if (!donnees.localisation) erreurs.push('Localisation');
    
    // Validations numériques
    if (donnees.nb_panneaux && (isNaN(donnees.nb_panneaux) || parseInt(donnees.nb_panneaux) < 1)) {
        erreurs.push('Nombre de panneaux doit être un nombre entier positif');
    }
    
    if (donnees.surface && (isNaN(donnees.surface) || parseFloat(donnees.surface) < 0)) {
        erreurs.push('Surface doit être un nombre positif');
    }
    
    if (donnees.puissance_crete && (isNaN(donnees.puissance_crete) || parseFloat(donnees.puissance_crete) < 0)) {
        erreurs.push('Puissance crête doit être un nombre positif');
    }
    
    if (donnees.orientation && (isNaN(donnees.orientation) || parseInt(donnees.orientation) < 0 || parseInt(donnees.orientation) > 360)) {
        erreurs.push('Orientation doit être entre 0 et 360 degrés');
    }
    
    if (donnees.pente && (isNaN(donnees.pente) || parseInt(donnees.pente) < 0 || parseInt(donnees.pente) > 90)) {
        erreurs.push('Pente doit être entre 0 et 90 degrés');
    }
    
    if (donnees.code_postal && !/^\d{5}$/.test(donnees.code_postal)) {
        erreurs.push('Code postal doit contenir 5 chiffres');
    }
    
    return erreurs;
}

// Fonction principale de sauvegarde
async function sauvegarderToutesModifications() {
    try {
        // Récupérer toutes les valeurs
        const donnees = recupererValeursFormulaire();
        console.log('Données à sauvegarder:', donnees);
        
        // Valider les données
        const erreurs = validerDonnees(donnees);
        if (erreurs.length > 0) {
            alert('Erreurs de validation:\n- ' + erreurs.join('\n- '));
            return;
        }
        
        // Désactiver le bouton pendant la sauvegarde
        const bouton = document.getElementById('btn-sauvegarder-global');
        bouton.disabled = true;
        bouton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Sauvegarde en cours...';
        
        // Préparer les données pour l'API
        const dataToSend = {
            action: 'update_installation',
            id: installationId,
            date_installation: donnees.date_installation + '-01', // Ajouter le jour
            nb_panneaux: parseInt(donnees.nb_panneaux),
            surface: parseFloat(donnees.surface),
            puissance_crete: parseFloat(donnees.puissance_crete),
            localisation: donnees.localisation
        };
        
        // Ajouter les champs optionnels s'ils sont remplis
        if (donnees.orientation) dataToSend.orientation = parseInt(donnees.orientation);
        if (donnees.pente) dataToSend.pente = parseInt(donnees.pente);
        if (donnees.latitude) dataToSend.latitude = parseFloat(donnees.latitude);
        if (donnees.longitude) dataToSend.longitude = parseFloat(donnees.longitude);
        if (donnees.code_postal) dataToSend.code_postal = donnees.code_postal;
        if (donnees.departement) dataToSend.code_departement = donnees.departement;
        if (donnees.region) dataToSend.code_region = donnees.region;
        if (donnees.marque_panneau) dataToSend.marque_panneau = donnees.marque_panneau;
        if (donnees.modele_panneau) dataToSend.modele_panneau = donnees.modele_panneau;
        if (donnees.marque_onduleur) dataToSend.marque_onduleur = donnees.marque_onduleur;
        if (donnees.modele_onduleur) dataToSend.modele_onduleur = donnees.modele_onduleur;
        if (donnees.nom_installateur) dataToSend.id_installateur = parseInt(donnees.nom_installateur);

        console.log('Données envoyées à l\'API:', dataToSend);
        
        // Envoyer la requête
        const response = await fetch('../api/endpoints/put.php', {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(dataToSend)
        });
        
        const responseText = await response.text();
        console.log('Réponse brute de l\'API:', responseText);
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Erreur de parsing de la réponse:', parseError);
            throw new Error('Réponse serveur invalide: ' + responseText);
        }
        
        if (result.success) {
            afficherMessage('Installation modifiée avec succès!' + 
                          (result.commune_utilisee ? '\nCommune utilisée: ' + result.commune_utilisee : ''), 'success');
            
            // Recharger les données pour refléter les changements
            await chargerInstallation();
            
            // Rediriger vers l'accueil après 2 secondes
            setTimeout(() => {
                window.location.href = 'accueil.php';
            }, 2000);
            
        } else {
            throw new Error(result.message || 'Erreur lors de la sauvegarde');
        }
        
    } catch (error) {
        console.error('Erreur lors de la sauvegarde:', error);
        afficherMessage('Erreur lors de la sauvegarde: ' + error.message, 'danger');
    } finally {
        // Réactiver le bouton
        const bouton = document.getElementById('btn-sauvegarder-global');
        if (bouton) {
            bouton.disabled = false;
            bouton.innerHTML = '<i class="fas fa-save me-2"></i>Sauvegarder toutes les modifications';
        }
    }
}

// Fonction utilitaire pour afficher les messages
function afficherMessage(message, type = 'success') {
    // Supprimer les anciens messages
    const anciensMessages = document.querySelectorAll('.alert');
    anciensMessages.forEach(msg => msg.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    `;
    
    const container = document.querySelector('main');
    container.insertBefore(alertDiv, container.firstChild);
    
    // Faire défiler vers le haut pour voir le message
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Supprimer automatiquement après 5 secondes
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Fonctions obsolètes conservées pour compatibilité (mais non utilisées)
function sauvegarderInstallation() {
    console.warn('Fonction obsolète - utiliser sauvegarderToutesModifications()');
}

function sauvegarderPlacement() {
    console.warn('Fonction obsolète - utiliser sauvegarderToutesModifications()');
}

function sauvegarderAdresse() {
    console.warn('Fonction obsolète - utiliser sauvegarderToutesModifications()');
}

function sauvegarderPanneau() {
    console.warn('Fonction obsolète - utiliser sauvegarderToutesModifications()');
}

function sauvegarderOnduleur() {
    console.warn('Fonction obsolète - utiliser sauvegarderToutesModifications()');
}