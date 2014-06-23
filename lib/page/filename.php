<?php
// filename creates the pathname of a wiki page
function filename($n = "", $prefix = "pages/") {
    if (empty($n)) $n = substr(request_path(), 1);

    return $prefix . preg_replace("; +;", " ", preg_replace(";/;", ".", 
        preg_replace(";[^a-z.];", "-", strtolower($n))));
}

