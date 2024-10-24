<?php

require_once('lib/autoload.php');

use afpa\APUnit;
use visums\Config;

APUnit::test(
  'storage of a value in Config',
  function(){
    $result = [0, []];
    Config::init();
    Config::set('toto', 2);

    try{
      $result[0]++;
      if(Config::get('toto') !== 2)
        $result[1][] = 'Expected integer 2, found ' . var_export(Config::get('toto'), true);
    }
    catch(\Exception $e){
        $result[1][] = 'Exception found : ' . $e->getMessage();
    }

    Config::set('myObj', new SplObjectStorage());
    try{
      $result[0]++;
      $o = Config::get('myObj');
      if(get_class($o) != 'SplObjectStorage')
        $result[1][] = 'Expected SplObjectStorage, found ' . get_class(Config::get('myObj'));

      if($o->count() > 0)
        $result[1][] = 'Expected empty SplObjectStorage, contains ' . $o->count() . ' objects';

      $o->attach(new SplObjectStorage(), 5);
      $o2 = Config::get('myObj');
      if($o2->count() != 1)
        $result[1][] = 'Expected one object SplObjectStorage, contains ' . $o2->count() . ' objects';
      var_export(Config::get('params4'));
      echo PHP_EOL;
      var_export(Config::get('params5'));
      echo PHP_EOL;
    }
    catch(\Exception $e){
        $result[1][] = 'Exception found : ' . $e->getMessage();
    }
    return $result;
  }
);
