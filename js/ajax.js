function request(callback,api,method,data=null){

    // Create XML HTTP request.
 let xhr = new XMLHttpRequest();
 xhr.open(method,api);
 xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');

 // Add onload function.
 xhr.onload = () => {
 switch (xhr.status) {
 case 200:
 case 201: callback(JSON.parse(xhr.responseText));
 break;
default: console.log('HTTP error: ' + xhr.status);
 }
 };

 // Send XML HTTP request.
 xhr.send();
}