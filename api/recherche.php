  <?php 
  
  require_once('query.php');

    // Database connection.
  $db = new Database();
  $pdo= $db->getConnection();

  if (!$pdo)
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
            if (isset($_GET['panneauS']) && isset($_GET['onduleurS']) && isset($_GET['departementS']))
          {
                  $data = dbListInstallation($pdo);
                  sendJsonData($data,200);
          }
          //An id is provided,fetching information about the installation
            if ($id && $id!='' && $requestRessource == 'installation')
          {
                  $data = dbRequestInstallation($pdo);
                  sendJsonData($data,200);
          }
            else 
          {
            //nothing is provided, adding options to the filters
                  $data = dbGetRandomValues($pdo);
                  sendJsonData($data,200);
          }}
      }
  }




   function sendJsonData($data, $status = 200)
  {
      if ($data && $status == 200) {
            //the headers
            header('Content-Type: application/json; charset=utf-8');
            header('Cache-control: no-store, no-cache, must-revalidate');
            header('Pragma: no-cache');
            header('HTTP/1.1 200 OK');
            $json=json_encode($data);
            //removes the unwanted backslashes in the json output
            $json=stripslashes($json);
            echo $json;
          }else{
              header('HTTP/1.1 400 Bad Request');

      }
  }

  ?>