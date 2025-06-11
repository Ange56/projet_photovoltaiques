document.addEventListener('DOMContentLoaded', () => {
    const anneeSelect = document.getElementById('annee');
    const departementSelect = document.getElementById('departement');
    const form = document.getElementById('filter-form');

    const map = L.map('map').setView([46.603354, 1.888334], 6); // France

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    let markersLayer = L.layerGroup().addTo(map);

    // 🔹 Charger les années
    fetch('../../api/endpoints/get.php?action=annees')
        .then(res => res.json())
        .then(data => {
            data.annees.forEach(item => {
                const option = document.createElement('option');
                option.value = item.annee;
                option.textContent = item.annee;
                anneeSelect.appendChild(option);
            });
        });

    // 🔹 Quand une année est sélectionnée → charger les départements
    anneeSelect.addEventListener('change', () => {
        const selectedYear = anneeSelect.value;
        departementSelect.innerHTML = '<option disabled selected>Chargement...</option>';

        fetch(`../../api/endpoints/get.php?action=departements_par_annee&annee=${selectedYear}`)
            .then(res => res.json())
            .then(data => {
                departementSelect.innerHTML = ''; // reset
                data.departements.forEach(dep => {
                    const option = document.createElement('option');
                    option.value = dep.code;
                    option.textContent = `${dep.code} - ${dep.nom}`;
                    departementSelect.appendChild(option);
                });
            });
    });

    // 🔹 Soumission du formulaire → charger les points sur la carte
    form.addEventListener('submit', e => {
        e.preventDefault();
        const annee = anneeSelect.value;
        const departement = departementSelect.value;

        fetch(`../../api/endpoints/get.php?action=installations_map&annee=${annee}&departement=${departement}`)
            .then(res => res.json())
            .then(data => {
                markersLayer.clearLayers();

                if (!data.length) {
                    alert("Aucune installation trouvée.");
                    return;
                }

                data.forEach(install => {
                    const marker = L.marker([install.lat, install.long]).addTo(markersLayer);
                    marker.bindPopup(`
                        <strong>${install.localite}</strong><br>
                        Puissance : ${install.puissance_crete} W<br>
                        <a href="../detail.html?nid=${install.id}">Voir détail</a>
                    `);
                });

                const bounds = L.latLngBounds(data.map(i => [i.lat, i.long]));
                map.fitBounds(bounds);
            });
    });
});
