<?php
// filename creates the pathname of a wiki page
function filename(string $n, $prefix = "wiki/"): string
{
    $n = trim($n, "/");
    $n = strtolower($n);
    $n = preg_replace(";[^a-z0-9/];", "_", $n);
    $n = preg_replace(";_+;", "_", $n);
    $n = preg_replace(";/+;", "/", $n);
    $n = $prefix . $n;
    return $n;
}

function firstln($file) {
    $f = fopen($file, 'r');
    $line = fgets($f);
    fclose($f);
    return trim($line);
}

// make current items bolded in a listing
function breadcrumb(array $listing, string $current = ""): array
{
    foreach ($listing as &$l) {
        $display = str_replace('.txt', '', basename($l));
        $href = str_replace(['wiki/', '//', '.txt'], ['/', '', ''], $l);
        if ($l == $current) {
            $l = "<a href=\"$href\"><b>$display</b></a>";
        } else {
            $l = "<a href=\"$href\">$display</a>";
        }
    }
    return array_reverse($listing);
}

// remove "index.txt" from listing
function globl(string $pattern)
{
    $files = glob($pattern);
    foreach ($files as $k => $c) {
        if (basename($c) == 'index.txt') {
            unset($files[$k]);
        }
    }
    return $files;
}
