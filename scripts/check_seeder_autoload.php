<?php

require __DIR__ . '/../vendor/autoload.php';

$target = 'Database\\Seeders\\ChatifyDemoSeeder';

echo 'class_exists(' . $target . '): ' . (class_exists($target) ? 'true' : 'false') . PHP_EOL;
