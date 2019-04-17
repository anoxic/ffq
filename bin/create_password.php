<?php

if ($argc < 2 || empty(getenv('UPASS')))
    die("Usage: $argv[0] USERNAME [page1[,pageX]] (note: requires env \$UPASS to be set)");

$hash = password_hash(getenv('UPASS'), PASSWORD_BCRYPT, ['salt' => sha1($argv[1])]);

file_put_contents("passwords", @"$argv[1]\t$hash\t$argv[2]", FILE_APPEND);

