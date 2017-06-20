<?php
function getfirst($file) {
    $f = fopen($file, 'r');
    $line = fgets($f);
    fclose($f);
    return trim($line);
}
