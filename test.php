<?php
$path = __DIR__ . '/protected/modules/backend/components/versions';
echo "Real path: $path\n";
echo "Exists: " . (is_dir($path) ? 'YES' : 'NO') . "\n";
$files = scandir($path);
print_r(array_diff($files, ['.', '..']));