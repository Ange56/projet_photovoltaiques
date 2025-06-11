// Variables globales pour les graphiques
let installationsParAnneeChart, installationsParRegionChart, installationsAnneeRegionChart;
let originalAnneeRegionData = [];
let availableYears = [];

// Fonction pour créer les cartes de statistiques
function createStatsCards(stats) {
    const statsContainer = document.getElementById('statsCards');
    statsContainer.innerHTML = `
        <div class="stat-card">
            <div class="stat-number">${stats.total_installations}</div>
            <div class="stat-label">Installations totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">${stats.total_installateurs}</div>
            <div class="stat-label">Installateurs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">${stats.total_marques_onduleurs}</div>
            <div class="stat-label">Marques d'onduleurs</div>
        </div>
        <div class="stat-card">
            <div class="stat-number">${stats.total_marques_panneaux}</div>
            <div class="stat-label">Marques de panneaux</div>
        </div>
    `;
}

// Fonction pour créer le graphique des installations par année
function createInstallationsParAnneeChart(data) {
    const canvas = document.getElementById('installationsParAnneeChart');
    if (!canvas) {
        console.error('Canvas installationsParAnneeChart not found');
        return;
    }
    const ctx = document.getElementById('installationsParAnneeChart').getContext('2d');
    
    if (installationsParAnneeChart) {
        installationsParAnneeChart.destroy();
    }

    installationsParAnneeChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.annee),
            datasets: [{
                label: 'Nombre d\'installations',
                data: data.map(item => item.count),
                backgroundColor: 'rgba(102, 126, 234, 0.6)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Fonction pour créer le graphique des installations par région
function createInstallationsParRegionChart(data) {
    const canvas = document.getElementById('installationsParRegionChart');
    if (!canvas) {
        console.error('Canvas installationsParRegionChart not found');
        return;
    }
    const ctx = document.getElementById('installationsParRegionChart').getContext('2d');
    
    if (installationsParRegionChart) {
        installationsParRegionChart.destroy();
    }

    installationsParRegionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: data.map(item => item.region),
            datasets: [{
                label: 'Nombre d\'installations',
                data: data.map(item => item.count),
                backgroundColor: 'rgba(102, 126, 234, 0.6)',
                borderColor: 'rgba(102, 126, 234, 1)',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });
}

// Fonction pour créer les options du menu déroulant
function createDropdownOptions() {
    const container = document.getElementById('dropdownContent');
    if (!container) return;
    
    container.innerHTML = '';
    
    availableYears.forEach(year => {
        const optionDiv = document.createElement('div');
        optionDiv.className = 'dropdown-option';
        
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = `year-${year}`;
        checkbox.value = year;
        checkbox.checked = true;
        checkbox.addEventListener('change', () => {
            updateAnneeRegionChart();
            updateDropdownText();
        });
        
        const label = document.createElement('label');
        label.htmlFor = `year-${year}`;
        label.textContent = year;
        
        optionDiv.appendChild(checkbox);
        optionDiv.appendChild(label);
        container.appendChild(optionDiv);
        
        // Empêcher la fermeture du dropdown quand on clique sur une option
        optionDiv.addEventListener('click', (e) => {
            e.stopPropagation();
            if (e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
                updateAnneeRegionChart();
                updateDropdownText();
            }
        });
    });
}

// Fonction pour gérer l'ouverture/fermeture du dropdown
function toggleDropdown() {
    const button = document.getElementById('dropdownButton');
    const content = document.getElementById('dropdownContent');
    const arrow = button.querySelector('.dropdown-arrow');
    
    if (!button || !content || !arrow) return;
    
    const isOpen = content.classList.contains('show');
    
    if (isOpen) {
        content.classList.remove('show');
        button.classList.remove('active');
        arrow.classList.remove('rotated');
    } else {
        content.classList.add('show');
        button.classList.add('active');
        arrow.classList.add('rotated');
    }
}

// Fonction pour mettre à jour le texte du dropdown
function updateDropdownText() {
    const selectedYears = getSelectedYears();
    const dropdownText = document.getElementById('dropdownText');
    
    if (!dropdownText) return;
    
    if (selectedYears.length === 0) {
        dropdownText.textContent = 'Aucune année sélectionnée';
    } else if (selectedYears.length === availableYears.length) {
        dropdownText.textContent = 'Toutes les années sélectionnées';
    } else if (selectedYears.length === 1) {
        dropdownText.textContent = `${selectedYears[0]} sélectionnée`;
    } else {
        dropdownText.innerHTML = `${selectedYears.length} années sélectionnées <span class="selected-count">(${selectedYears.join(', ')})</span>`;
    }
}

// Fonction pour obtenir les années sélectionnées
function getSelectedYears() {
    const checkboxes = document.querySelectorAll('#dropdownContent input[type="checkbox"]:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Fonction pour sélectionner toutes les années
function selectAllYears() {
    const checkboxes = document.querySelectorAll('#dropdownContent input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = true);
    updateAnneeRegionChart();
    updateDropdownText();
}

// Fonction pour désélectionner toutes les années
function deselectAllYears() {
    const checkboxes = document.querySelectorAll('#dropdownContent input[type="checkbox"]');
    checkboxes.forEach(cb => cb.checked = false);
    updateAnneeRegionChart();
    updateDropdownText();
}

// Fonction pour mettre à jour le graphique selon les années sélectionnées
function updateAnneeRegionChart() {
    const selectedYears = getSelectedYears();
    
    if (selectedYears.length === 0) {
        if (installationsAnneeRegionChart) {
            installationsAnneeRegionChart.destroy();
        }
        return;
    }
    
    // const filteredData = originalAnneeRegionData.filter(item => selectedYears.includes(item.annee));
    const filteredData = originalAnneeRegionData.filter(item => selectedYears.includes(item.annee.toString()));

    createInstallationsAnneeRegionChart(filteredData);
}

// Fonction pour créer le graphique des installations par année et région
function createInstallationsAnneeRegionChart(data) {
    const canvas = document.getElementById('installationsAnneeRegionChart');
    if (!canvas) {
        console.error('Canvas installationsAnneeRegionChart not found');
        return;
    }
    const ctx = document.getElementById('installationsAnneeRegionChart').getContext('2d');
    
    if (installationsAnneeRegionChart) {
        installationsAnneeRegionChart.destroy();
    }

    if (!data || data.length === 0) {
        console.warn('Aucune donnée pour le graphique année-région');
        return;
    }

    const regions = [...new Set(data.map(item => item.region))];
    const annees = [...new Set(data.map(item => item.annee))].sort();
    
    const colors = [
        'rgba(255, 99, 132, 0.6)',
        'rgba(54, 162, 235, 0.6)',
        'rgba(255, 205, 86, 0.6)',
        'rgba(75, 192, 192, 0.6)',
        'rgba(153, 102, 255, 0.6)',
        'rgba(255, 159, 64, 0.6)',
        'rgba(199, 199, 199, 0.6)',
        'rgba(83, 102, 255, 0.6)',
        'rgba(255, 99, 255, 0.6)',
        'rgba(99, 255, 132, 0.6)'
    ];

    const datasets = regions.map((region, index) => {
        const regionData = annees.map(annee => {
            const found = data.find(item => item.region === region && item.annee === annee);
            return found ? parseInt(found.count) : 0;
        });

        return {
            label: region,
            data: regionData,
            backgroundColor: colors[index % colors.length],
            borderColor: colors[index % colors.length].replace('0.6', '1'),
            borderWidth: 2
        };
    });

    installationsAnneeRegionChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: annees,
            datasets: datasets
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}

// Fonction pour initialiser les événements du dropdown
function initializeDropdownEvents() {
    const dropdownButton = document.getElementById('dropdownButton');
    if (dropdownButton) {
        dropdownButton.addEventListener('click', toggleDropdown);
    }
    
    // Fermer le dropdown si on clique ailleurs
    document.addEventListener('click', (e) => {
        const dropdown = document.querySelector('.dropdown-container');
        if (dropdown && !dropdown.contains(e.target)) {
            const content = document.getElementById('dropdownContent');
            const button = document.getElementById('dropdownButton');
            const arrow = button?.querySelector('.dropdown-arrow');
            
            if (content && button && arrow) {
                content.classList.remove('show');
                button.classList.remove('active');
                arrow.classList.remove('rotated');
            }
        }
    });
}

// Fonction pour charger toutes les statistiques
async function loadStatistics() {
    try {
        // Statistiques générales
        const statsRes = await fetch('../../api/endpoints/get.php?action=stats_generales');
        const statsData = await statsRes.json();
        createStatsCards(statsData.general);

        // Installations par année
        const anneeRes = await fetch('../../api/endpoints/get.php?action=installations_par_annee');
        const anneeData = await anneeRes.json();
        createInstallationsParAnneeChart(anneeData);

        // Installations par région
        const regionRes = await fetch('../../api/endpoints/get.php?action=installations_par_region');
        const regionData = await regionRes.json();
        createInstallationsParRegionChart(regionData);

        // Installations par année et région
        const anneeRegionRes = await fetch('../../api/endpoints/get.php?action=installations_annee_region');
        const anneeRegionData = await anneeRegionRes.json();
        
        // Stocker les données originales
        originalAnneeRegionData = anneeRegionData;
        
        // Extraire les années disponibles
        availableYears = [...new Set(anneeRegionData.map(item => item.annee))].sort();
        
        // Créer les options du dropdown
        createDropdownOptions();
        
        // Mettre à jour le texte du dropdown
        updateDropdownText();
        
        // Initialiser les événements
        initializeDropdownEvents();
        
        // Créer le graphique initial avec toutes les données
        createInstallationsAnneeRegionChart(anneeRegionData);

    } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        document.getElementById('statsCards').innerHTML = '<div class="loading">Erreur lors du chargement des données</div>';
    }
}

// Charger les statistiques au chargement de la page
document.addEventListener('DOMContentLoaded', loadStatistics);