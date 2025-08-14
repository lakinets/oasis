<?php
require __DIR__ . '/vendor/autoload.php';

echo "class_exists('app\\\\modules\\\\backend\\\\assets\\\\BackendAsset') => ";
var_dump(class_exists('app\\modules\\backend\\assets\\BackendAsset'));

echo "<br>autoload namespaces:<br>";
print_r(require __DIR__ . '/vendor/composer/autoload_psr4.php');
