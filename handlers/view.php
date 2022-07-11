<?php
$children = [];
[$found, $file, $path] = tryfind($uri, $children);

$list = [];
$p    = dirname($path);
$gp   = dirname($p);

if ($gp != '.') {
    $list[] = breadcrumb(globl("$gp/*"), $p);
}

if ($p != '.') {
    $list[] = breadcrumb(globl("$p/*"), $path);
}

if (count($children ?? [])) {
    $list[] = breadcrumb($children);
}

if (isset($file)) {
    [$meta, $htm] = render($file);
}
?>
<!doctype html>
<html lang=en>
<head>
    <?=require('src/meta.php')?>
    <title><?=$meta['title'] ?? 'Not Found'?></title>
    <style>
    nav {
     width: 100%;
     float: left;
     border-bottom: 2px groove gray;
     margin-bottom: 1em;
    }
    .bread { list-style: none; float: left; padding: 0 1em 0 0; }
    </style>
</head>
<body>
    <?php
    if (isset($list)) {
        echo "<nav>";
        foreach ($list as $col) {
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
    ?>
</body>
</html>
