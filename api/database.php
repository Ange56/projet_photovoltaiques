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

  function dbRequestInstallation($db, $id = '')
{
    try
    {
      if ($id != '')
        $request = ' SELECT * FROM tweets WHERE id=:id';
      $statement = $db->prepare($request);
        $statement->bindParam(':id', $login, PDO::PARAM_STR, 20);
      $statement->execute();
      $result = $statement->fetchAll(PDO::FETCH_ASSOC);
      else{
        $request = ' SELECT * FROM tweets';
      }
    
    catch (PDOException $exception)
    {
      error_log('Request error: '.$exception->getMessage());
      return false;
    }
    return $result;
    }
}