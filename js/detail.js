///param installation the details of an installation
///removes all the nulls in the result



///param installation the details of an installation
///displays the details of an installation
function display(installation){
    let installationMenu = document.getElementById("installationm");
    let placementMenu = document.getElementById("placementm");
    let panneauMenu = document.getElementById("panneaum");
    let adresseMenu = document.getElementById("adressem");
    let ondulateurMenu = document.getElementById("onduleurm");

    let infodoc= document.getElementById("infodoc");

    //adds the id of the document
    infodoc.innerText+= " "+installation['iddoc']

    //adds the different sections and their content
    installationMenu.innerHTML+=
    "<tr>" +
        "<td>Date d'installation</td>" +
        "<td>"+installation['mois_installation']+"/"+installation['an_installation']+"</td>" +
    "</tr>"+
    "<tr>" +
        "<td>Surface</td>" +
        "<td>"+installation['surface']+"</td>" +
    "</tr>"+
    "<tr>" +
        "<td>Puissance crete</td>" +
        "<td>"+installation['puissance_crete']+"</td>" +
    "</tr>"

    placementMenu.innerHTML+=
    "<tr>" +
        "<td>Orientation</td>" +
        "<td>"+installation['orientation']+"</td></tr>"+
    "<tr>" +
        "<td>Orientation optimum</td>" +
        "<td>"+installation['orientation_optimum']+"</td></tr>"+
    "<tr>" +
        "<td>Pente</td>" +
        "<td>"+installation['pente']+"</td></tr>"+
    "<tr>" +
        "<td>Pente optimum</td>" +
        "<td>"+installation['pente_optimum']+"</td></tr>"+
    "<tr>" +
        "<td>Installateur</td>" +
        "<td>"+installation['installateur']+"</td></tr>"+
    "<tr>" +
        "<td>Production (PVGIS)</td>" +
        "<td>"+installation['production_pvgis']+"</td></tr>"

    panneauMenu.innerHTML+=
    "<tr>" +
        "<td>Modèle</td>" +
        "<td>"+installation['nom_modele']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Marque</td>" +
        "<td>"+installation['nom_panneau']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Nombre</td>" +
        "<td>"+installation['nb_panneaux']+"</td>" +
        "</tr>"

    adresseMenu.innerHTML+=
    "<tr>" +
        "<td>Lattitude</td>" +
        "<td>"+installation['lat']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Longitude</td>" +
        "<td>"+installation['long']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Localité</td>" +
        "<td>"+installation['nom_standard']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Département</td>" +
        "<td>"+installation['nom_departement']+
        "</tr>"+
        "<td>Pays</td>" +
        "<td>"+installation['pays']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Code postal</td>" +
        "<td>"+installation['code_postal']+
        "</tr>"+
    "<tr>" +
        "<td>Region</td>" +
        "<td>"+installation['nom_region']+
        "</tr>"+
    "<tr>" +
        "<td>Pays</td>" +
        "<td>"+installation['pays']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Zone administrative</td>" +
        "<td>"+installation['administrative_area_level_1']+"/"+installation['administrative_area_level_2']+"/"+installation['administrative_area_level_3']+"/"+installation['administrative_area_level_4']+"</td>" +
        "</tr>"+
        "<tr>" +
    "<tr>" +
        "<td>Political</td>" +
        "<td>"+installation['political']+"</td>" +
        "</tr>"

    ondulateurMenu.innerHTML+=
    "<tr>" +
        "<td>Modèle</td>" +
        "<td>"+installation['nom_onduleur']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Marque</td>" +
        "<td>"+installation['nom_Marque_onduleur']+"</td>" +
        "</tr>"+
    "<tr>" +
        "<td>Nombre</td>" +
        "<td>"+installation['nb_onduleur']+"</td>" +
        "</tr>"
}

///fetches the information about the selected installation to display and gives the buttons their functionality
window.addEventListener("load",function(){

    let urlParams= new URLSearchParams(this.window.location.search);

    let id = urlParams.get('id');

    let info= document.getElementById("info");

    let installationMenu = document.getElementById("installationm");
    let placementMenu = document.getElementById("placementm");
    let panneauMenu = document.getElementById("panneaum");
    let adresseMenu = document.getElementById("adressem");
    let ondulateurMenu = document.getElementById("onduleurm");

    let installationButton = document.getElementById("installationb");
    let placementButton = document.getElementById("placementb");
    let adresseButton = document.getElementById("adresseb");
    let ondulateurButton = document.getElementById("onduleurb");
    let panneauButton = document.getElementById("panneaub")


    info.innerText+= " "+id;


    request("GET",'../../api/endpoints/get.php?action=installation_detail',display,id);

    installationButton.onclick = function(){installationMenu.classList.toggle('d-none')};
    placementButton.onclick = function(){placementMenu.classList.toggle('d-none')};
    adresseButton.onclick = function(){adresseMenu.classList.toggle('d-none')};
    panneauButton.onclick = function(){panneauMenu.classList.toggle('d-none')};
    ondulateurButton.onclick = function(){ondulateurMenu.classList.toggle('d-none')};

})

