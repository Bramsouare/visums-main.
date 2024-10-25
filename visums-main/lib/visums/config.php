<?php
declare(strict_types=1); // typage strict pour garantir que les types des arguments et des retours de fonctions sont respectés exactement.

namespace Visums; // organiser le code et déviter les conflits.

abstract class Config { // Déclare une classe abstraite `Config` que l'on ne peut pas instancier directement.

  static protected array $data = []; // Tableau statique pour stocker les données de configuration.
  static protected array $components = []; // Tableau statique pour stocker les instances de composants.

  static public function saveEnvFile(string $fn) : void {
    // Méthode qui doit sauvegarder la configuration dans un fichier d'environnement. Non implémentée.
    throw new \Exception("NOT IMPLEMENTED"); // Lance une exception pour signaler que la méthode n'est pas encore développée.
  }

  static public function getValue(string $name) : mixed { 
    // Méthode pour obtenir une valeur de configuration.
    if(empty(static::$data)) // Si le tableau de données est vide, initialise les valeurs.
      static::init();

    return static::$data[$name] ?? null; // Renvoie la valeur si elle existe, sinon `null`.
  }

  static public function setValue(string $name, mixed $value) : void { 
    // Méthode pour définir une valeur de configuration.
    static::$data[$name] = $value; // Stocke la valeur dans le tableau `$data` avec le nom spécifié.
  }

  static public function datas() : array { 
    // Renvoie toutes les données de configuration.
    if(empty(static::$data)) // Si les données n'ont pas encore été chargées, les initialise.
      static::init();

    return static::$data; // Renvoie toutes les données.
  }

  static public function get(string $name) : mixed { 
    // Obtient un composant basé sur son nom.
    if(!isset(static::$components[$name])){ 
      // Si le composant n'est pas déjà instancié, il est créé.
      $class = '\\Visums\\Core\\' . strtoupper(substr($name, 0, 1)) . substr($name, 1); 
      // Construit le nom de la classe du composant en utilisant une convention de nommage.
      $component = new $class(); // Instancie le composant.

      static::$components[$name] = $component; // Stocke l'instance du composant dans `$components`.
    }

    return static::$components[$name]; // Renvoie l'instance du composant.
  }

  static public function setComponent(string $name, mixed $component) : void { 
    // Associe un composant à un nom.
    $interface = '\\Visums\\Interfaces\\I' . strtoupper(substr($name, 0, 1)) . substr($name, 1); 
    // Construit le nom de l'interface correspondante.
    if(is_a($component, $interface)) // Vérifie si le composant implémente bien l'interface attendue.
      static::$components[$name] = $component; // Si oui, stocke le composant.
    else {
      // TODO / DECIDE : Invalid object for mission
      // Si le composant ne correspond pas à l'interface, il faudrait définir le comportement attendu.
    }
  }

  static public function init() : void { 
    // Initialise les valeurs de configuration.
    static::readEnvFile('.env'); // Lit et charge les valeurs du fichier `.env`.
    static::readEnvFile('.env.local'); // Lit et charge les valeurs du fichier `.env.local`.
  }

  static protected function readEnvFile(string $fn) : void { 
    // Méthode protégée qui lit un fichier d'environnement et enregistre ses valeurs.
    $content = file_get_contents($fn); // Lit le contenu du fichier.
    $lines = explode("\n", $content); // Divise le contenu en lignes.

    foreach($lines as $noline => $line) { 
      // Parcourt chaque ligne du fichier.
      if(substr($line, 0, 1) !== '#') { 
        // Ignore les lignes de commentaires qui commencent par `#`.
        $words = explode('=', $line); // Divise la ligne en un nom et une valeur (avant et après `=`).
        $nb = count($words); // Compte le nombre de parties trouvées.

        if($nb > 1) { // Si une valeur est définie (si `=` est présent dans la ligne).
          if($nb > 2) 
            $words = [$words[0], implode('=', array_slice($words, 1))]; 
          // Si la ligne contient plusieurs `=`, les valeurs suivantes sont regroupées.

          $pos = strpos($words[1], '#'); 
          if($pos !== FALSE) { 
            // Si un commentaire est présent dans la valeur.
            if(
              preg_match('/^[^\'"]*#/', $words[1]) != FALSE || 
              preg_match('/#[^\'"]*$/', $words[1]) != FALSE
            ){
                $words[1] = substr($words[1], 0, $pos); 
                // Retire le commentaire pour ne garder que la valeur.
            }
          }

          $words[1] = trim($words[1], " \n\r\t\v\x00'\""); 
          // Supprime les espaces et les guillemets autour de la valeur.

          Config::setValue($words[0], $words[1]); 
          // Enregistre la variable et sa valeur dans `$data`.
        }
      }
    }
  }
}
