

///param installation the details of an installation
///displays the details of an installation
function display(installation){
    let installationMenu = document.getElementById("installationm");
    let placementMenu = document.getElementById("placementm");
    let panneauMenu = document.getElementById("panneaum");
    let adresseMenu = document.getElementById("adressem");
    let ondulateurMenu = document.getElementById("onduleurm");

    let infodoc= document.getElementById("infodoc");

    infodoc.innerText+= installation['iddoc']

    installationMenu.innerHTML+=
    "<tr><td>Date d'installation</td><td>"+installation['mois_installation']+"/"+installation['an_installation']+"</td></tr>"+
    "<tr><td>Surface</td><td>"+installation['surface']+"</td></tr>"+
    "<tr><td>Puissance crete</td><td>"+installation['puissance_crete']+"</td></tr>"+
    "<tr><td>Localisation</td><td>"+installation['locality']+"</td></tr>"

    placementMenu.innerHTML+=
    "<tr><td>Orientation</td><td>"+installation['orientation']+"</td></tr>"+
    "<tr><td>Orientation optimum</td><td>"+installation['orientation_optimum']+"</td></tr>"+
    "<tr><td>Pente</td><td>"+installation['pente']+"</td></tr>"+
    "<tr><td>Pente optimum</td><td>"+installation['pente_optimum']+"</td></tr>"+
    "<tr><td>Installateur</td><td>"+installation['installateur']+"</td></tr>"+
    "<tr><td>Production (PVGIS)</td><td>"+installation['production_pvgis']+"</td></tr>"

    panneauMenu.innerHTML+=
    "<tr><td>Modèle</td><td>"+installation['panneaux_modele']+"</td></tr>"+
    "<tr><td>Marque</td><td>"+installation['panneaux_marque']+"</td></tr>"+
    "<tr><td>Nombre</td><td>"+installation['nb_panneaux']+"</td></tr>"

    adresseMenu.innerHTML+=
    "<tr><td>Lattitude</td><td>"+installation['lat']+"</td></tr>"+
    "<tr><td>Longitude</td><td>"+installation['lon']+"</td></tr>"+
    "<tr><td>Localité</td><td>"+installation['locality']+"</td></tr>"+
    "<tr><td>Code postal</td><td>"+installation['postal_town']+installation['postal_code']+installation['postal_code_suffix']+"</td></tr>"+
    "<tr><td>Zone administrative</td><td>"+installation['administrative_area_level_1']+installation['administrative_area_level_2']+installation['administrative_area_level_3']+installation['administrative_area_level_4']+"</td></tr>"+
    "<tr><td>Pays</td><td>"+installation['country']+"</td></tr>"+
    "<tr><td>Political</td><td>"+installation['political']+"</td></tr>"

    ondulateurMenu.innerHTML+=
    "<tr><td>Modèle</td><td>"+installation['onduleur_modele']+"</td></tr>"+
    "<tr><td>Marque</td><td>"+installation['onduleur_marque']+"</td></tr>"+
    "<tr><td>Nombre</td><td>"+installation['nb_onduleur']+"</td></tr>"
}

///param menu 
///toggles between opening and closing a menu
function toggle(menu){
    if (menu.style.display==none){menu.style.display=block}
    else {menu.style.display==none}
}


///fetches the information abotu the selected installation to display and gives the buttons their functionality
window.addEventListener("load",function(){

    let urlParams= new URLSearchParams(this.window.location.search);

    let id = urlParams.get('id')

    let info= document.getElementById("info")

    let installationMenu = document.getElementById("installationm");
    let placementMenu = document.getElementById("placementm");
    let panneauMenu = document.getElementById("panneaum");
    let adresseMenu = document.getElementById("adressem");
    let ondulateurMenu = document.getElementById("onduleurm");

    let installationButton = document.getElementById("installationb");
    let placementButton = document.getElementById("placementb");
    let panneauButton = document.getElementById("panneaub");
    let adresseButton = document.getElementById("adresseb");
    let ondulateurButton = document.getElementById("onduleurb");

    info.innerText+= id

    request(display,'../api/request.php/detail',"GET",id)


    installationButton.onclick = toggle(installationMenu);
    placementButton.onclick = toggle(placementMenu);
    adresseButton.onclick = toggle(adresseMenu);
    panneauButton.onclick = toggle(panneauMenu);
    ondulateurButton.onclick = toggle(ondulateurMenu);
    

})

