  <?php 
  
  require_once('database.php');

    // Database connection.
  $db = dbConnect();
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
          $element = array_shift($request);

          // Polls request.
          if ($requestRessource == 'installations')
          {
                  $data = dbRequestTweets($db, $element);
                  sendJsonData($data,200);
          }
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