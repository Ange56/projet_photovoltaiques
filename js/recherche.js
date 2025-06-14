document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('searchForm');
    const marqueOnduleurSelect = document.getElementById('marqueOnduleur');
    const marquePanneauSelect = document.getElementById('marquePanneau');
    const departementSelect = document.getElementById('departement');
    const table = document.getElementById('resultatsTable');
    const tbody = table.querySelector('tbody');

    // 🔹 Fonction utilitaire de fetch
    async function fetchData(url) {
        console.log("Fetching URL:", url);
        try {
            const res = await fetch(url);
            if (!res.ok) throw new Error(`HTTP ${res.status}`);
            return await res.json();
        } catch (err) {
            console.error("Erreur de fetch:", err);
            alert("Erreur lors du chargement des données.");
            return null;
        }
    }

    // 🔹 Charger les filtres (marques & départements)
    async function loadFiltres() {
        // Chargement des marques d'onduleurs
        const onduleurs = await fetchData('../../api/endpoints/get.php?action=marques_onduleurs');
        console.log("Données onduleurs :", onduleurs);

        if (onduleurs?.marques_onduleurs) {
            onduleurs.marques_onduleurs.forEach(marque => {
                marqueOnduleurSelect.append(new Option(marque.nom, marque.nom));
            });
        }

        // Chargement des marques de panneaux
        const panneaux = await fetchData('../../api/endpoints/get.php?action=marques_panneaux');
        console.log("Données panneaux :", panneaux);

        if (panneaux?.marques_panneaux) {
            panneaux.marques_panneaux.forEach(marque => {
                marquePanneauSelect.append(new Option(marque.nom, marque.nom));
            });
        }

        // Chargement des départements (aléatoire)
        const departements = await fetchData('../../api/endpoints/get.php?action=departements_random');
        console.log("Données départements :", departements);

        if (departements?.departements) {
            departements.departements.forEach(dep => {
                departementSelect.append(new Option(`${dep.code} - ${dep.nom}`, dep.code));
            });
        }
    }

    // 🔍 Soumission du formulaire
    form.addEventListener('submit', async e => {
        e.preventDefault();
        console.log("Formulaire soumis !");





        const marqueOnduleur = marqueOnduleurSelect.value;
        const marquePanneau = marquePanneauSelect.value;
        const departement = departementSelect.value;
        const debut= document.getElementById("debut");
        const avant= document.getElementById("avant");
        const apres= document.getElementById("apres");
        const positionInfo = document.getElementById("positionInfo");
        const nombreInfo = document.getElementById("nombre")

        let position = 0;
        let nombre = nombreInfo.value


        document.getElementById("pagination").classList.remove("d-none")

        //Pour que les boutons et l'input changent la page
        debut.onclick = () => {position=0; results()};
        avant.onclick = () => {position--; results()};
        apres.onclick = () => {position++; results()};
        positionInfo.oninput = () => {position = positionInfo.value; results()}
        positionInfo.oninput = () => {position = positionInfo.value; results()}
        nombreInfo.oninput = () => {nombre =  nombreInfo.value; results()}


        const params = new URLSearchParams({
            action: "recherche_installations",
            ...(marqueOnduleur && { marque_onduleur: marqueOnduleur }),
            ...(marquePanneau && { marque_panneau: marquePanneau }),
            ...(departement && { departement: departement }),
            });

        //affiche la page actuelle et son contenu
        async function results(){
            nombre=document.getElementById("nombre").value
            positionInfo.value=position;
            const data = await fetchData(`../../api/endpoints/get.php?${params.toString()}&position=`+position+"&nombre="+nombre);
            const errorDiv = document.getElementById('error-message');
            errorDiv.classList.add('d-none');
            errorDiv.textContent = "";

            tbody.innerHTML = "";
            table.classList.add('d-none');

            if (!data || data.length === 0) {
                errorDiv.textContent = "Aucun résultat trouvé pour les critères sélectionnés.";
                errorDiv.classList.remove('d-none');
                return;
            }

            data.forEach(inst => {
                const row = document.createElement('tr');
                row.innerHTML = `
            <td>${inst.localite}</td>
            <td>${inst.departement}</td>
            <td>${inst.puissance_crete} kWc</td>
            <td><a href="../html/detail.html?id=${inst.id}" class="btn-custom-detail">Voir</a></td>
        `;
                tbody.appendChild(row);
            });

            table.classList.remove('d-none');}
        await results()
    });




    // Démarrage
    loadFiltres();
});