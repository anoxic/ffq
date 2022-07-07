<meta charset=utf-8>
<style>
nav {
 width: 100%;
 float: left;
 border-bottom: 2px groove gray;
 margin-bottom: 1em;
}
.bread { list-style: none; float: left; padding: 0 1em 0 0; }
</style>
<?php

$found = 0;
$path = filename($uri);
$listing = [];

if (file_exists($i = filename($uri, "aka/") . ".txt")) {
    $found++;
    $path = filename(firstln($i));
}

if (file_exists($i = "$path.txt")) {
    $found++;
    $file = $i;
}

if (is_dir($path)) {
    $found++;

    $children = globl("$path/*");
    
    if (file_exists($i = "$path/index.txt")) {
        $file = $i;
    }
}


$p  = dirname($path);
$gp = dirname($p);

if ($gp != '.') {
    $listing[] = breadcrumb(globl("$gp/*"), $p);
}

if ($p != '.') {
    $listing[] = breadcrumb(globl("$p/*"), $path);
}

if (count($children ?? [])) {
    $listing[] = breadcrumb($children);
}

if (isset($file)) {
    [$meta, $htm] = render($file);
    echo "<title>$meta[title]</title>";
}

if (isset($listing)) {
    echo "<nav>";
    foreach ($listing as $col) {
        echo "<ol class=\"bread\">";
        foreach ($col as $item) {
            echo "<li>$item</li>";
        }
        echo "</ol>";
    }
    echo "</nav>";
}

if (isset($file)) {
    if (isset($meta['title'])) {
        echo "<h1>$meta[title]</h1>";
    }
    echo $htm;
}

if (!$found) {
    $code = 404;
    echo "<h1 title=$path>404</h1>";
}
