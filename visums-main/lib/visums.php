<?php

require_once('autoload.php');

function getDSN() : string{
  return Config::get('DSN');
}
