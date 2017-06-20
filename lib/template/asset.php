<?php
function asset($a)
{
    $x = "assets/$a";
    if (file_exists($x)) {
        require($x);
    }
}
