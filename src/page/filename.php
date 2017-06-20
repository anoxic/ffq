<?php
// filename creates the pathname of a wiki page
function filename($n = "", $prefix = "../pages/") {
    if (empty($n)) {
        $n = substr(request_path(), 1);
    }

    $n = strtolower($n);
    $n = preg_replace(";/;", ".", $n);
    $n = preg_replace(";[^a-z0-9.];", "-", $n);
    $n = preg_replace("; +;", " ", $n);
    $n = $prefix . $n;

    return $n;
}

