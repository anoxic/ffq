<?php
// session() Gets and sets session variables
function session($prop, $val = null) {
    if (!session_id()) session_start();

    if (isset($_SESSION[$prop]) && !$val)
        return $_SESSION[$prop];

    $_SESSION[$prop] = $val;
}

