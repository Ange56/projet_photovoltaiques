// Variables globales pour les graphiques
let installationsParAnneeChart, installationsParRegionChart, installationsAnneeRegionChart;

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

// Fonction pour créer le graphique des installations par année et région (en barres)
function createInstallationsAnneeRegionChart(data) {
    const ctx = document.getElementById('installationsAnneeRegionChart').getContext('2d');
    
    if (installationsAnneeRegionChart) {
        installationsAnneeRegionChart.destroy();
    }

    // Organiser les données par région
    const regions = [...new Set(data.map(item => item.region))];
    const annees = [...new Set(data.map(item => item.annee))].sort();
    
    const datasets = regions.map((region, index) => {
        const regionData = annees.map(annee => {
            const found = data.find(item => item.region === region && item.annee === annee);
            return found ? found.count : 0;
        });

        const colors = [
            'rgba(255, 99, 132, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(255, 205, 86, 0.6)',
            'rgba(75, 192, 192, 0.6)',
            'rgba(153, 102, 255, 0.6)'
        ];

        return {
            label: region,
            data: regionData,
            backgroundColor: colors[index % colors.length],
            borderColor: colors[index % colors.length].replace('0.6', '1'),
            borderWidth: 2
        };
    });

    installationsAnneeRegionChart = new Chart(ctx, {
        type: 'bar', // Changé de 'line' à 'bar'
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
        createInstallationsAnneeRegionChart(anneeRegionData);

    } catch (error) {
        console.error('Erreur lors du chargement des statistiques:', error);
        document.getElementById('statsCards').innerHTML = '<div class="loading">Erreur lors du chargement des données</div>';
    }
}


// Charger les statistiques au chargement de la page
document.addEventListener('DOMContentLoaded', loadStatistics);





