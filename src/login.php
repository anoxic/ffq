<?php

function login($user, $pw)
{
    $file = 'conf/logins/' . filename($user, '') . '.txt';
    if (file_exists($file)) {
        $hash = firstln($file, 'pw');
        return password_verify($pw, $hash);
    }
    return false;
}
