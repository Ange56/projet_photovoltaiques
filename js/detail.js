/*

///param data the details of an installation
///displays the details of an installation
function display(data){
    let installationMenu = document.getElementById("installationm");
    let placementMenu = document.getElementById("placementm");
    let panneauMenu = document.getElementById("panneaum");
    let adresseMenu = document.getElementById("adressem");
    let ondulateurMenu = document.getElementById("onduleurm");

    installationMenu.innerHTML+=
    "<tr><td>Date d'installation</td><td>"+data['']+"/"+data['']+"/"+data['']+"</td></tr>"+
    "<tr><td>Surface</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Puissance crete</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Localisation</td><td>"+data['']+"</td></tr>"

    placementMenu.innerHTML+=
    "<tr><td>Orientation</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Orientation optimum</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Pente</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Pente optimum</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Installateur</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Production (PVGIS)</td><td>"+data['']+"</td></tr>"

    panneauMenu.innerHTML+=
    "<tr><td>Modèle</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Marque</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Nombre</td><td>"+data['']+"</td></tr>"

    adresseMenu.innerHTML+=
    "<tr><td>Lattitude</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Longitude</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Localité</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Code postal</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Zone administrative</td><td>"+data['']+data['']+data['']+data['']+"</td></tr>"+
    "<tr><td>Pays</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Political</td><td>"+data['']+"</td></tr>"

    ondulateurMenu.innerHTML+=
    "<tr><td>Modèle</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Marque</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Nombre</td><td>"+data['']+"</td></tr>"
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

    request(display,'../api/request.php/installations',"GET",id)


    installationButton.onclick = toggle(installationMenu);
    placementButton.onclick = toggle(placementMenu);
    adresseButton.onclick = toggle(adresseMenu);
    panneauButton.onclick = toggle(panneauMenu);
    ondulateurButton.onclick = toggle(ondulateurMenu);
    

})

*/