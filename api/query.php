<?php 

require_once('config/database.php')

  ///Used to find information about an installation that has a specific id
  function dbRequestInstallation($db, $id = '')
{
    try
    {
      if ($id != ''){
        $request = ' SELECT i.*, o.*, p.*, c.*, d.*, r.* FROM Installation i 
        INNER JOIN Onduleur o ON o.id_onduleur= i.id_onduleur
        INNER JOIN Panneau p ON p.id_panneau= i.id_panneau
        INNER JOIN Communes c ON c.code_insee = i.code_insee
        INNER JOIN Departement d ON d.code = c.code;
        INNER JOIN Region r ON r.code = d.code_Region
        WHERE i.id=:id';
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
      
        $request = ' SELECT i.mois_installation, i.an_installation, i.nb_panneaux, i.surface, i.puissance_crete, c.nom_standard  FROM Installation i
        INNER JOIN Onduleur o ON o.id_onduleur= i.id_onduleur
        INNER JOIN Panneau p ON p.id_panneau= i.id_panneau
        INNER JOIN Communes c ON c.code_insee = i.code_insee
        INNER JOIN Departement d ON d.code = c.code';
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
        $request = ' SELECT DISTINCT d.nom, p.nom, o.nom FROM Installation i
        INNER JOIN Onduleur o ON o.id_onduleur= i.id_onduleur
        INNER JOIN Panneau p ON o.id_panneau= i.id_panneau
        INNER JOIN Communes c ON c.code_insee = i.code_insee
        INNER JOIN Departement d ON d.code = c.code
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

