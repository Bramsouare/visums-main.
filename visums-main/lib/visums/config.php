<?php
declare(strict_types=1);

namespace Visums;

abstract class Config{
  static protected array $data = [];
  static protected array $components = [];

  static public function saveEnvFile(string $fn) : void{
    // TODO
    throw new \Exception("NOT IMPLEMENTED");
  }

  static public function getValue(string $name) : mixed{
    if(empty(static::$data))
      static::init();

    // return isset(static::$data[$name])?static::$data[$name]:null;
    return static::$data[$name] ?? null;
  }

  static public function setValue(string $name, mixed $value) : void{
    static::$data[$name] = $value;
  }

  static public function datas() : array{
    if(empty(static::$data))
      static::init();

    return static::$data;
  }

  static public function get(string $name) : mixed{
    if(!isset(static::$components[$name])){
      // TODO : unknown component in Visums ?
      $class = '\\Visums\\Core\\' . strtoupper(substr($name, 0, 1)) . substr($name, 1);
      $component = new $class();

      static::$components[$name] = $component;
    }

    return static::$components[$name];
  }

  static public function setComponent(string $name, mixed $component) : void{
    $interface = '\\Visums\\Interfaces\\I' . strtoupper(substr($name, 0, 1)) . substr($name, 1);
    if(is_a($component, $interface))
      static::$components[$name] = $component;
    else{
      // TODO / DECIDE : Invalid object for mission
    }
  }

  static public function init() : void{
    static::readEnvFile('.env');
    static::readEnvFile('.env.local');
  }

  static protected function readEnvFile(string $fn) : void{
    $content = file_get_contents($fn);
    $lines = explode("\n", $content);

    foreach($lines as $noline => $line){
      if(substr($line, 0, 1) !== '#'){
        $words = explode('=', $line);
        $nb = count($words);
        if($nb > 1){
          if($nb > 2)
            $words = [$words[0], implode('=', array_slice($words, 1))];

          $pos = strpos($words[1], '#');
          if($pos !== FALSE){
            if(
              preg_match('/^[^\'"]*#/', $words[1]) != FALSE ||
              preg_match('/#[^\'"]*$/', $words[1]) != FALSE
            ){
                $words[1] = substr($words[1], 0, $pos);
            }
          }
          $words[1] = trim($words[1], " \n\r\t\v\x00'\"");
          // TODO ?? indexed table to interpret
          // TODO ?? hashtable to interpret

          Config::setValue($words[0], $words[1]);
        }
      }
    }
  }
}
