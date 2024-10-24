<?php
declare(strict_types=1); // typage strict attendus.

namespace Visums\Core; // éviter les conflits de noms entre différentes classes.

use Visums\Interfaces\IDb; // Db devra implémenter IDb.
use Visums\Config; // accéder aux valeurs de configuration.
use PDO; // gérer les connexions à la base de données en PHP.

class Db implements IDb { // oblige la classe à définir certaines méthodes.

  protected $connexion; // protégée pour stocker la connexion PDO à la base de données.
  protected $statement; // protégée pour stocker la requête SQL préparée (PDOStatement).

  public function __construct(){ // automatiquement appelé lorsqu'une instance de Db est créée.
    // Récupère la valeur 'DSN' dans la configuration via Config. Le DSN est la chaîne utilisée pour se connecter à la base de données.
    $dsn = Config::getValue('DSN'); 

    if(is_null($dsn)){ // Vérifiela valeur du DSN est nulle (ce qui signifie qu'il n'a pas été configuré).
      // Si le DSN est nul, on récupère les informations de connexion.
      $dbconnect = [ 
        'dbtype' => '', 'dbuser' => '', 'dbpassword' => '', 
        'dbhost' => '', 'dbport' => '', 'db' => '',
      ];

      foreach($dbconnect as $k => $v) // Parcourt chaque clé du tableau $dbconnect.
        // chaque clé, récupère la valeur correspondante dans la configuration (par exemple, 'dbtype', 'dbuser').
        $dbconnect[$k] = Config::getValue($k); 

      $dbconnect = array_filter($dbconnect); // supprime les valeurs vides (null, '', false).
      if(count($dbconnect) < 6){ // Vérifie si le tableau contient moins de 6 éléments après filtrage (ce qui signifie que certaines informations de connexion manquent).
        // Si les informations de connexion sont incomplètes, une exception est lancée.
        throw new \Exception('Incomplete Db Configuration'); 
      }

      // Si toutes les informations de connexion sont présentes, construit le DSN à partir des éléments du tableau.
      $dsn = sprintf(
        '%s://%s:%p@%s:%s/%s', // Format du DSN (type://user:password@host:port/database).
        $dbconnect['dbtype'], $dbconnect['dbuser'], $dbconnect['dbpassword'], 
        $dbconnect['dbhost'], $dbconnect['dbport'], $dbconnect['db']
      );
    }

    // Utilise la classe PDO pour créer une connexion à la base de données.
    // Le DSN est passé, ainsi que l'utilisateur et le mot de passe, récupérés via Config::getValue.
    $this->connexion = new PDO($dsn, Config::getValue('dbuser'), Config::getValue('dbpassword'));
  }

  public function query(string $sql, array $data = []){ // Méthode pour préparer et exécuter une requête SQL avec des paramètres optionnels.
    $this->statement = $this->connexion->prepare($sql); // Prépare la requête SQL via PDO. Cela empêche les injections SQL.
    if($this->statement === FALSE){ // Si la préparation de la requête échoue (retourne FALSE).
      // Lance une exception indiquant qu'il y a eu une erreur lors de la préparation de la requête.
      throw new \Exception(sprintf('Query Prepare Error : %s', $sql));
    }
    // Exécute la requête SQL préparée en passant les paramètres (le tableau $data).
    $this->statement->execute($data);
  }

  public function fetchAll(){ // Méthode pour récupérer tous les résultats d'une requête.
    // Retourne tous les résultats sous forme de tableau associatif (clé => valeur).
    return $this->statement->fetchAll(PDO::FETCH_ASSOC); 
  }

  public function fetch(){ // Méthode pour récupérer un seul résultat de la requête.
    // Retourne une seule ligne du résultat sous forme de tableau associatif (clé => valeur).
    return $this->statement->fetch(PDO::FETCH_ASSOC);
  }
}
