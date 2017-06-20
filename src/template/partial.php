<?php
function partial($a, array $context = [])
{
    extract($context);
    $x = "../views/partials/$a";
    if (file_exists($x)) {
        require($x);
    }
}

