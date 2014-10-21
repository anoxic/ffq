<?php
// g() Fetches GET and POST variables
function g($prop = "") {
    if (empty($prop))            return $_REQUEST;
    if (isset($_REQUEST[$prop])) return $_REQUEST[$prop];
}

