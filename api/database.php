<?php 

require_once('constants.php')

  // Create the connection to the database.
  // return False on error and the database otherwise.
  function dbConnect()
  {
    try
    {
      $db = new PDO('pgsql:host='.DB_SERVER.';port='.DB_PORT.';dbname='.DB_NAME, DB_USER, DB_PASSWORD);
      $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch (PDOException $exception)
    {
      error_log('Connection error: '.$exception->getMessage());
      return false;
    }
    return $db;
  }


  ///Used to find information about an installation that has a specific id
  function dbRequestInstallation($db, $id = '')
{
    try
    {
      if ($id != ''){
        $request = ' SELECT * FROM - WHERE id=:id';
      $statement = $db->prepare($request);
        $statement->bindParam(':id', $login, PDO::PARAM_STR, 20);
      $statement->execute();
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);}
      else{
        return false;
      }
    
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $result;
    }
}

///Lists all installations that match a a filter
 function dbListInstallation($db, $departement='any', $onduleur='any' ,$panneau='any', $count=50 )
{
    try
    {
      //adds the chosen filters
      
        $request = ' SELECT * FROM -';
        if($departement!='any'){
          $request.="WHERE = :department"
        }
        if($onduleur!='any'){
          $request.="WHERE = :onduleur"
        }
        if($panneau!='any'){
          $request.="WHERE = :panneau"
        }

      $statement .= "LIMIT :count;"
      $statement = $db->prepare($request);


      //adds the filters' values
      if($departement!='any'){
           $statement->bindParam(':departement', $departement, PDO::PARAM_STR);
        }
        if($onduleur!='any'){
           $statement->bindParam(':onduleur', $onduleur, PDO::PARAM_STR);
        }
        if($panneau!='any'){
           $statement->bindParam(':panneau', $panneau, PDO::PARAM_STR);
        }
        $statement->bindParam(':count', $count);
       
      $statement->execute();
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);
      else{
        return false;
      }
    
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $result;
    }
}

///gets a specific amount of random values to be used as filter options
function dbGetRandomValues($count=20){
  try
    {
        $request = ' SELECT * FROM -
        ORDER BY RAND()
        LIMIT :count;';
      $statement = $db->prepare($request);
        $statement->bindParam(':count', $count);
      $statement->execute();
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);
    
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $result;
    }
}

