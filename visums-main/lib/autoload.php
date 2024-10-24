<?php

set_include_path(get_include_path() . PATH_SEPARATOR . './');

spl_autoload_register(
  function($class){
    $folders = [ 'lib', 'libext', ];
    $filename = str_replace("\\", DIRECTORY_SEPARATOR, strtolower($class)) . '.php';

    foreach($folders as $folder){
      $fn = $folder . DIRECTORY_SEPARATOR . $filename;
      if(file_exists($fn)){
        require_once($fn);
        return true;
      }
    }

    return false;
  }
);
