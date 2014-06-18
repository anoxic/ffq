<?php
// g() Fetches GET and POST variables
function g($prop = "") {
    return (isset($_REQUEST[$prop])) ? $_REQUEST[$prop] : $_REQUEST;
}

