<?php
  require_once __DIR__ .'/vendor/autoload.php';

  use Felis\Silvestris\Database as DB;
  use Felis\Silvestris\Session;

  $db = DB::connect('mysql');
  
?>
