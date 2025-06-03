//addsboptions to a select
function addOpt(select,options){
    options.array.forEach(element => {
        select.innerhtml+= <option id="option">element</option>
        
    });
    

}


document.addEventListener("load", function(){
let selecti;
let selectd;
let selectp;

    if(selecti){
        let data= request(addOpt,,"GET")
    }
    if(selectd){}
    if(selectp){}

})