
///param select the select where the options are going to be added
///the options that are going to be added
///adds options to a select

let selecto=document.getElementById("onduleurS");
let selectd=document.getElementById("departementS");
let selectp=document.getElementById("panneauS");

function addOpt(select,options){
    options.forEach(element => {
        select.innerHTML+= "<option id="+element+">"+element+"</option>"
        
    });
}

function fillFilters(data){
    console.log(data);

    //sets are used to remove duplicates for each filter
    let panneauO= new Set(data['panneau'])
    let onduleurO= new Set(data['onduleur'])
    let depertementO= new Set(data['departement'])

    addOpt(selectp,panneauO);
    addOpt(selectd,depertementO);
    addOpt(selecto,onduleurO);
}


///param result the elements that match the query
///lists the matching results
function display(result){
    let list= document.getElementById("list");
       list.innerHTML+="<tr><td>Date d'installation</td><td>Nombre de panneaux</td><td>Surface</td><td>Puissance crête</td><td>Localisation</td><td><td>en savoir plus</td></tr>"
    result.forEach(element=>{
        list.innerHTML+="<tr><td>"+element['mois_installation']+"/"+element['an_installation']+"</td><td>"+element['nb_panneaux']+"</td><td>"+element['surface']+"</td><td>"+element['puissance_crete']+"</td><td>"+element['nom_standard']+"</td><td>"+"</td><td><a href='detail.html?id="+encodeURIComponent(element['id'])+"'></a></td></tr>"

    })


}

///when the window is loaded, the filters are filled and given functionality
window.addEventListener("load", function(){
    //used for filling the filters

    console.log("a")
    if(selecto && selectd && selectp){
        
        request("GET",'/projet_photovoltaiques/api/recherche.php/installation',fillFilters)}

    //used to fetch the results the matching filters
    let form=document.getElementById("selection")
    form.addEventListener("submit",request("GET",'/projet_photovoltaiques/api/recherche.php/installation',display))
}
)
