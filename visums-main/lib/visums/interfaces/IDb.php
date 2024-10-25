<?php
declare(strict_types=1); // typage strict attendus.

namespace Visums\Interfaces; // organiser le code et d'éviter les conflits de noms.

interface IDb { // définit un ensemble de méthodes que toute classe qui l'implémente doit définir.

  public function query(string $sql, array $data = []); 
  // Toute classe implémentant cette interface doit définir cette méthode.
  // Elle prend deux paramètres :
  // - $sql : une chaîne (string) qui représente la requête SQL.
  // - $data : un tableau optionnel (array), qui par défaut est vide, 
  // utilisé pour passer des paramètres à la requête.

  public function fetchAll();
  // Déclare la méthode `fetchAll`. Cette méthode ne prend pas de paramètres.
  // Elle est censée renvoyer tous les résultats d'une requête SQL sous une forme déterminée 
  // (généralement un tableau associatif).

  public function fetch();
  // Déclare la méthode `fetch`. Cette méthode ne prend pas de paramètres.
  // Elle est censée renvoyer un seul résultat d'une requête SQL, 
  //souvent sous forme de tableau associatif représentant une ligne de résultat.
}
