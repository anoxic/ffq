<?php
// pagename creates a "human" page name from a filename
function pagename($n) {
    $n = preg_replace("/^-/", "~", $n);
    $n = strtr($n, "-.", " /");
    return $n;
}

