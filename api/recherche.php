  <?php 
  
  require_once('query.php');

    // Database connection.
  $db = getConnection();
  if (!$db)
  {
    header('HTTP/1.1 503 Service Unavailable');
    exit;
  }
  else{
    $requestMethod = $_SERVER['REQUEST_METHOD'];
      $request = substr($_SERVER['PATH_INFO'], 1);
      $request = explode('/', $request);
      $requestRessource = array_shift($request);



      if ($requestMethod === 'GET'){
          $id = array_shift($request);

          
          if ($requestRessource == 'installation')
          {
            //the filters have defined values, listing the matching results
            if ($_GET['panneauS'] && $_GET['onduleurS'] && $_GET['departementS'])
          {
                  $data = dbListInstallation()
                  sendJsonData($data,200);
          }
          //An id is provided,fetching information about the installation
            if ($id && $id!='')
          {
                  $data = dbRequestInstallation();
                  sendJsonData($data,200);
          }
            else 
          {
            //nothing is provided, adding options to the filters
                  $data = dbGetRandomValues();
                  sendJsonData($data,200);
          }}
      }
  }




   function sendJsonData($data, $status = 200)
  {
      if ($data) {

            header('Content-Type: text/plain; charset=utf-8');
            header('Cache-control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('HTTP/1.1 200 OK');
            echo json_encode($data);
          }else{
              header('HTTP/1.1 400 Bad Request');

      }
  }

  ?>