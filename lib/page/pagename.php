<?php
// pagename creates a "human" page name from a filename
function pagename($n) {
    return ucwords($n = preg_replace(";^ ;", "~",
        str_replace("-", " ", str_replace(".", "/", $n))));
}

