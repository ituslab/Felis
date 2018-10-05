<?php
  /* Fungsi Autoload Module */
  spl_autoload_register(function($module){
    $packagePath = __DIR__.'/';
    $notPackages = ['.', '..'];
    $packages = array_diff(scandir($packagePath), $notPackages);
    foreach ($packages as $package) {
      $modulePath = $packagePath.$package;
      if (is_dir($modulePath)) $file = "{$modulePath}/{$module}.php";
      else $file = "{$packagePath}/{$module}.php";
      if (file_exists($file)) { require_once($file); break; }
    }
  });
?>
