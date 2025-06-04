/*
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
        list.innerHTML+="<tr><td>"+element['']+"/"+element['']+"/"+element['']+"</td><td>"+element['']+"</td><td>"+element['']+"</td><td>"+element['']+"</td><td>"+element['']+"</td><td>"+element['']+"</td><td>"+element['']+"</td><td>"+element['']+"</td><td><a href='detail.html?id="+encodeURIComponent(element['id'])+"'></a></td></tr>"

    })


}


document.addEventListener("load", function(){
let selecti=document.getElementById("seli");
let selectd=document.getElementById("seld");
let selectp=document.getElementById("selp");

    if(selecti){
        request(addOpt,selecti,api,"GET")

    }
    if(selectd){
        request(addOpt,selectd,api,"GET")

    }
    if(selectp){
        request(addOpt,selectp,api,"GET")}

    let form=document.getElementById("selection")
    form.addEventListener("submit",request(display,'../api/request.php/installations',"GET"))
    
}

)
*/