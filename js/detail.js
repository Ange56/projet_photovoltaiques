///param data the details of an installation
///displays the details of an installation
function display(data){
    let installationm = document.getElementById("menui");
    let placementm = document.getElementById("menupl");
    let panneaum = document.getElementById("menupa");
    let adressem = document.getElementById("menua");
    let ondulateurm = document.getElementById("menuo");

    installationm.innerHTML+=
    "<tr><td>Date d'installation</td><td>"+data['']+"/"+data['']+"/"+data['']+"</td></tr>"+
    "<tr><td>Surface</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Puissance crete</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Localisation</td><td>"+data['']+"</td></tr>"

    placementm.innerHTML+=
    "<tr><td>Orientation</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Orientation optimum</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Pente</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Pente optimum</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Installateur</td><td>"+data['']+"</td></tr>"+
    "<tr><td></td>Production (PVGIS)<td>"+data['']+"</td></tr>"

    panneaum.innerHTML+=
    "<tr><td>Modèle</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Marque</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Nombre</td><td>"+data['']+"</td></tr>"

    adressem.innerHTML+=
    "<tr><td>Lattitude</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Longitude</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Localité</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Code postal</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Zone administrative</td><td>"+data['']+data['']+data['']+data['']+"</td></tr>"+
    "<tr><td>Pays</td><td>"+data['']+"</td></tr>"+
    "<tr><td>Political</td><td>"+data['']+"</td></tr>"

    ondulateurm.innerHTML+=
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


///fetches the information to display and gives the buttons their functionality
window.addEventListener("load",function(){
    let installationm = document.getElementById("menui");
    let placementm = document.getElementById("menupl");
    let panneaum = document.getElementById("menupa");
    let adressem = document.getElementById("menua");
    let ondulateurm = document.getElementById("menuo");

    let installationb = document.getElementById("buttoni");
    let placementb = document.getElementById("buttonpl");
    let panneaub = document.getElementById("buttonpa");
    let adresseb = document.getElementById("buttona");
    let ondulateurb = document.getElementById("buttono");

    request(display,'../api/request.php/installations',"GET",)


    installationb.onclick = toggle(installationm);
    placementb.onclick = toggle(placementm);
    adresseb.onclick = toggle(adressem);
    panneaub.onclick = toggle(panneaum);
    ondulateurb.onclick = toggle(ondulateurm);
    

})