
///param select the select where the options are gonna be added
///the options that are gonna be added
///adds options to a select

function addOpt(select,options){
    options.forEach(element => {
        select.innerHTML+= "<option id="+element+">"+element+"</option>"
        
    });
    

}

///param result the elements that matches the query
///lists the matching results
function display(result){
    let list= getElementById("list");
       list.innerHTML+="<tr><td>Date d'installation</td><td>Nombre de panneaux</td><td>Surface</td><td>Puissance crête</td><td>Localisation</td><td><td>en savoir plus</td></tr>"
    result.forEach(element=>{
        list.innerHTML+="<tr><td>"+element['mois_installation']+"/"+element['an_installation']+"</td><td>"+element['nb_panneaux']+"</td><td>"+element['surface']+"</td><td>"+element['puissance_crete']+"</td><td>"+element['locality']+"</td><td>"+"</td><td><a href='detail.html?id="+encodeURIComponent(element['id'])+"'></a></td></tr>"

    })


}


document.addEventListener("load", function(){
let selecti=document.getElementById("onduleurS");
let selectd=document.getElementById("departementS");
let selectp=document.getElementById("panneauS");

    if(selecti && selectd && selectp){
        request(addOpt,'../api/request.php/filtre',"GET")}

    let form=document.getElementById("selection")
    form.addEventListener("submit",request(display,'../api/request.php/liste',"GET")) 
}
)
