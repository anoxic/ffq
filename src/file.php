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
        $_l = str_replace('.txt', '', basename($l));
        if ($l == $current) {
            $l = "<b>$_l</b>";
        } else {
            $l = $_l;
        }
    }
    return array_reverse($listing);
}
