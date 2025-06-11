
function request(method,api,callback,data){

    // Create XML HTTP request.
 let xhr = new XMLHttpRequest();
 if (method === 'GET' && data != null)
 {api += '&id=' + data}

 xhr.open(method,api);
 xhr.setRequestHeader('Content-Type','application/x-www-form-urlencoded');
 
 // Add onload function.
 xhr.onload = () => {
 switch (xhr.status) {
 case 200:
 case 201:
  console.log(xhr.responseText);
  callback(JSON.parse(xhr.responseText));
 break;
 default: httpErrors(xhr.status);
 }};

 // Send XML HTTP request.
 xhr.send();
}



//to have more deltails about errors

function httpErrors(errorCode)
{
 let messages =
     {
      400: 'Requête incorrecte',
      401: 'Authentifiez vous',
      403: 'Accès refusé',
      404: 'Page non trouvée',
      500: 'Erreur interne du serveur',
      503: 'Service indisponible'
     };

 // Display error.
 if (errorCode in messages)
 {
  $('#errors').html('<i class="fa fa-exclamation-circle"></i> <strong>' +
      messages[errorCode] + '</strong>');
  $('#errors').show();
 }
}
