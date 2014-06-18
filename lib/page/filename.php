<?php
// filename creates the pathname of a wiki page
function filename($n = "", $prefix = "pages/") {
    if ($n < 0) $prefix = "";
    if (empty($n) || $n < 0) $n = substr(request_path(), 1);

    return $prefix . preg_replace("; +;", " ", preg_replace(";/;", ".", 
        preg_replace(";[^a-z.];", "-", strtolower($n))));
}

