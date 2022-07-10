<?php

if ($session('wiki_user')) {
    echo "logged in";
} else {
    echo "access denied (not logged in)";
    $code = 401;
}
