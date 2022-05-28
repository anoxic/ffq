<?php

$found = 0;
$path = filename($uri);

if (file_exists($i = filename($uri, "aka/") . ".txt")) {
    $found++;
    $path = "wiki/" . firstln($i);
}

if (file_exists($i = "$path.txt")) {
    $found++;
    echo nl2br(file_get_contents($i));
}

if (is_dir($path)) {
    $found++;
    if (file_exists($i = "$path/index.txt")) {
        echo nl2br(file_get_contents($i));
    }
}

if (!$found) {
    $code = 404;
    echo "<h1 title=$path>404</h1>";
}
