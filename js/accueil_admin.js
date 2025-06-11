document.addEventListener('DOMContentLoaded', () => {
    // Éléments DOM
    const loadingState = document.getElementById('loadingState');
    const dataContainer = document.getElementById('dataContainer');
    const emptyState = document.getElementById('emptyState');
    const dataTableBody = document.getElementById('dataTableBody');
    const btnAddInstallation = document.getElementById('btnAddInstallation');
    const installationModal = document.getElementById('installationModal');
    const installationForm = document.getElementById('installationForm');
    const btnCloseModal = document.getElementById('btnCloseModal');
    const btnCancel = document.getElementById('btnCancel');
    const notification = document.getElementById('notification');
    const notificationMessage = document.getElementById('notificationMessage');
    const closeNotification = document.getElementById('closeNotification');
    const modalTitle = document.getElementById('modalTitle');
    const btnSubmit = document.getElementById('btnSubmit');
    const localiteInput = document.getElementById('localite');

    let installations = [];
    // let isEditing = false;
    // let editingId = null;
    let communes = [];

    // Fonction utilitaire de fetch
    async function fetchData(url, options = {}) {
        try {
            const response = await fetch(url, {
                headers: {
                    'Content-Type': 'application/json',
                },
                ...options
            });
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('Erreur de fetch:', error);
            showNotification('Erreur de connexion au serveur', 'error');
            return null;
        }
    }

    // Charger la liste des communes pour l'autocomplétion
    async function loadCommunes() {
        const data = await fetchData('../api/endpoints/get.php?action=communes_list');
        if (data && data.communes) {
            communes = data.communes;
            setupAutoComplete();
        }
    }

    // Configuration de l'autocomplétion
    function setupAutoComplete() {
        const datalist = document.createElement('datalist');
        datalist.id = 'communes-list';
        
        communes.forEach(commune => {
            const option = document.createElement('option');
            option.value = commune.nom_standard;
            datalist.appendChild(option);
        });
        
        document.body.appendChild(datalist);
        localiteInput.setAttribute('list', 'communes-list');
        

        localiteInput.addEventListener('input', function(e) {
            const value = e.target.value.toLowerCase();
            
            datalist.innerHTML = '';
            
            const filteredCommunes = communes.filter(commune => 
                commune.nom_standard.toLowerCase().includes(value)
            ).slice(0, 10);
            
            filteredCommunes.forEach(commune => {
                const option = document.createElement('option');
                option.value = commune.nom_standard;
                datalist.appendChild(option);
            });

            // Auto-remplir les informations de la commune sélectionnée
            const selectedCommune = communes.find(c => 
                c.nom_standard.toLowerCase() === value.toLowerCase()
            );
            
            if (selectedCommune) {
                // Suggestion automatique mais champs modifiables
                const codePostalInput = document.getElementById('codePostal');
                const departementInput = document.getElementById('departement');
                const regionInput = document.getElementById('region');
                
                // Remplir seulement si les champs sont vides
                if (!codePostalInput.value && selectedCommune.code_postal) {
                    codePostalInput.value = selectedCommune.code_postal;
                }
                if (!departementInput.value && selectedCommune.departement) {
                    departementInput.value = selectedCommune.departement;
                }
                if (!regionInput.value && selectedCommune.region) {
                    regionInput.value = selectedCommune.region;
                }
            }
        });
    }

    // Afficher les notifications
    function showNotification(message, type = 'success') {
        notificationMessage.textContent = message;
        notification.className = `notification ${type}`;
        notification.style.display = 'block';
        
        setTimeout(() => {
            notification.style.display = 'none';
        }, 5000);
    }

    // Charger les installations
    async function loadInstallations() {
        showLoading(true);
        
        const data = await fetchData('../api/endpoints/get.php?action=all');
        
        if (data) {
            console.log('Nombre d\'installations reçues:', data.length);
            console.log('Données reçues:', data);
            
            installations = data;
            renderTable();
        }
        
        showLoading(false);
    }

    // Afficher/masquer le loading
    function showLoading(show) {
        if (show) {
            loadingState.style.display = 'block';
            dataContainer.style.display = 'none';
            emptyState.style.display = 'none';
        } else {
            loadingState.style.display = 'none';
        }
    }

    // Rendre le tableau
    function renderTable() {
        if (installations.length === 0) {
            dataContainer.style.display = 'none';
            emptyState.style.display = 'block';
            return;
        }

        console.log('Nombre d\'installations à afficher:', installations.length);

        emptyState.style.display = 'none';
        dataContainer.style.display = 'block';
        
        dataTableBody.innerHTML = '';
        
        installations.forEach((installation, index) => {
            if (index < 5) {
                console.log(`Installation ${index + 1}:`, installation);
            }
            
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${installation.an_installation || 'N/A'}</td>
                <td>${installation.nb_panneaux || 'N/A'}</td>
                <td>${installation.surface || 'N/A'}</td>
                <td>${installation.puissance_crete|| 'N/A'}</td>
                <td>${installation.localite|| 'N/A'}</td>
                <td>
                    <button class="btn-edit" onclick="editInstallation(${installation.id})" title="Modifier">
                        ✏️
                    </button>
                    <button class="btn-edit" onclick="visuInstallation(${installation.id})" title="Visualiser">
                        👁️
                    </button>
                    <button class="btn-delete" onclick="deleteInstallation(${installation.id})" title="Supprimer">
                        🗑️
                    </button>
                </td>
            `;
            dataTableBody.appendChild(row);
        });
        
        console.log('Nombre de lignes ajoutées au tableau:', dataTableBody.children.length);
    }

    // Ouvrir le modal d'ajout
    function openAddModal() {
        // isEditing = false;
        // editingId = null;
        modalTitle.textContent = 'Nouvelle installation';
        btnSubmit.textContent = 'Ajouter l\'installation';
        installationForm.reset();
        //document.getElementById('installationId').value = '';
        
        // Valeurs par défaut
        document.getElementById('pays').value = 'France';
        document.getElementById('orientation').value = 180; // Sud par défaut
        document.getElementById('pente').value = 30; // 30° par défaut
        document.getElementById('nbOnduleur').value = 1;
        
        installationModal.style.display = 'block';
    }

    // Ouvrir le modal d'édition
    window.editInstallation = async function(id) {
        isEditing = true;
        editingId = id;
        modalTitle.textContent = 'Modifier l\'installation';
        btnSubmit.textContent = 'Modifier l\'installation';
        
        const data = await fetchData(`../api/endpoints/get.php?action=installation_detail&id=${id}`);
        
        if (data) {
            // ... remplissage des champs du modal
        }
        
        installationModal.style.display = 'block';
    };

    // PAR cette nouvelle version (redirection) :
    window.editInstallation = function(id) {
        // Redirection vers la page de modification avec l'ID en paramètre
        window.location.href = `../back/modification.php?id=${id}`;
    };

    // Fermer le modal
    function closeModal() {
        installationModal.style.display = 'none';
        installationForm.reset();
        isEditing = false;
        editingId = null;
    }

    // Ajouter une installation
    async function addInstallation(formData) {
        const data = await fetchData('../api/endpoints/post.php', {
            method: 'POST',
            body: JSON.stringify({
                action: 'add_installation',
                // Informations générales
                mois_installation: parseInt(formData.installMonth),
                an_installation: parseInt(formData.installYear),
                surface: parseFloat(formData.surface),
                puissance_crete: parseFloat(formData.puissanceCrete),
                
                // Placement et orientation
                orientation: parseInt(formData.orientation),
                orientation_optimum: formData.orientationOptimum ? parseInt(formData.orientationOptimum) : null,
                pente: parseInt(formData.pente),
                pente_optimum: formData.penteOptimum ? parseInt(formData.penteOptimum) : null,
                installateur: formData.installateur || null,
                production_pvgis: formData.productionPvgis ? parseInt(formData.productionPvgis) : null,
                
                // Localisation
                latitude: formData.latitude ? parseFloat(formData.latitude) : null,
                longitude: formData.longitude ? parseFloat(formData.longitude) : null,
                localite: formData.localite,
                departement: formData.departement || null,
                code_postal: formData.codePostal || null,
                region: formData.region || null,
                pays: formData.pays || 'France',
                
                // Panneaux
                modele_panneau: formData.modeleReponse,
                marque_panneau: formData.marquePanneau,
                nb_panneaux: parseInt(formData.nbPanneaux),
                
                // Onduleurs
                modele_onduleur: formData.modeleOnduleur,
                marque_onduleur: formData.marqueOnduleur,
                nb_onduleur: parseInt(formData.nbOnduleur)
            })
        });

        if (data && data.success) {
            showNotification('Installation ajoutée avec succès');
            closeModal();
            loadInstallations();
        } else {
            showNotification(data?.message || 'Erreur lors de l\'ajout', 'error');
        }
    }

    // Modifier une installation
    async function updateInstallation(formData) {
        const data = await fetchData('../api/endpoints/put.php', {
            method: 'PUT',
            body: JSON.stringify({
                action: 'update_installation',
                id: editingId,
                // Mêmes données que pour l'ajout
                mois_installation: parseInt(formData.installMonth),
                an_installation: parseInt(formData.installYear),
                surface: parseFloat(formData.surface),
                puissance_crete: parseFloat(formData.puissanceCrete),
                orientation: parseInt(formData.orientation),
                orientation_optimum: formData.orientationOptimum ? parseInt(formData.orientationOptimum) : null,
                pente: parseInt(formData.pente),
                pente_optimum: formData.penteOptimum ? parseInt(formData.penteOptimum) : null,
                installateur: formData.installateur || null,
                production_pvgis: formData.productionPvgis ? parseInt(formData.productionPvgis) : null,
                latitude: formData.latitude ? parseFloat(formData.latitude) : null,
                longitude: formData.longitude ? parseFloat(formData.longitude) : null,
                localite: formData.localite,
                departement: formData.departement || null,
                code_postal: formData.codePostal || null,
                region: formData.region || null,
                pays: formData.pays || 'France',
                modele_panneau: formData.modeleReponse,
                marque_panneau: formData.marquePanneau,
                nb_panneaux: parseInt(formData.nbPanneaux),
                modele_onduleur: formData.modeleOnduleur,
                marque_onduleur: formData.marqueOnduleur,
                nb_onduleur: parseInt(formData.nbOnduleur)
            })
        });

        if (data && data.success) {
            showNotification('Installation modifiée avec succès');
            closeModal();
            loadInstallations();
        } else {
            showNotification(data?.message || 'Erreur lors de la modification', 'error');
        }
    }

    // Visualiser une installation 
    window.visuInstallation = function(id) {
        window.location.href = `../back/detail_admin.php?id=${id}`;
    };

    // Supprimer une installation
    window.deleteInstallation = async function(id) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cette installation ?')) {
            return;
        }

        const data = await fetchData('../api/endpoints/delete.php', {
            method: 'DELETE',
            body: JSON.stringify({
                action: 'delete_installation',
                id: id
            })
        });

        if (data && data.success) {
            showNotification('Installation supprimée avec succès');
            loadInstallations();
        } else {
            showNotification(data?.message || 'Erreur lors de la suppression', 'error');
        }
    };

    // Gestionnaire de soumission du formulaire
    installationForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = {
            installMonth: document.getElementById('installMonth').value,
            installYear: document.getElementById('installYear').value,
            surface: document.getElementById('surface').value,
            puissanceCrete: document.getElementById('puissanceCrete').value,
            orientation: document.getElementById('orientation').value,
            orientationOptimum: document.getElementById('orientationOptimum').value,
            pente: document.getElementById('pente').value,
            penteOptimum: document.getElementById('penteOptimum').value,
            installateur: document.getElementById('installateur').value,
            productionPvgis: document.getElementById('productionPvgis').value,
            latitude: document.getElementById('latitude').value,
            longitude: document.getElementById('longitude').value,
            localite: document.getElementById('localite').value,
            departement: document.getElementById('departement').value,
            codePostal: document.getElementById('codePostal').value,
            region: document.getElementById('region').value,
            pays: document.getElementById('pays').value,
            modeleReponse: document.getElementById('modeleReponse').value,
            marquePanneau: document.getElementById('marquePanneau').value,
            nbPanneaux: document.getElementById('nbPanneaux').value,
            modeleOnduleur: document.getElementById('modeleOnduleur').value,
            marqueOnduleur: document.getElementById('marqueOnduleur').value,
            nbOnduleur: document.getElementById('nbOnduleur').value
        };

        // Validation basique des champs obligatoires
        const requiredFields = [
            'installMonth', 'installYear', 'surface', 'puissanceCrete', 
            'orientation', 'pente', 'localite', 'modeleReponse', 
            'marquePanneau', 'nbPanneaux', 'modeleOnduleur', 
            'marqueOnduleur', 'nbOnduleur'
        ];
        
        for (let field of requiredFields) {
            if (!formData[field] || formData[field].toString().trim() === '') {
                showNotification(`Le champ ${field} est requis`, 'error');
                return;
            }
        }

        // if (isEditing) {
        //     await updateInstallation(formData);
        // } else {
        //     await addInstallation(formData);
        // }
        await addInstallation(formData);

    });

    // Gestionnaires d'événements
    btnAddInstallation.addEventListener('click', openAddModal);
    btnCloseModal.addEventListener('click', closeModal);
    btnCancel.addEventListener('click', closeModal);
    closeNotification.addEventListener('click', () => {
        notification.style.display = 'none';
    });

    // Fermer le modal en cliquant à l'extérieur
    window.addEventListener('click', (e) => {
        if (e.target === installationModal) {
            closeModal();
        }
    });

    // Initialisation
    loadInstallations();
    loadCommunes();
});