<?php
// markdown compiles and extend markdown into html
function markdown($_) {
    $_ = preg_replace(
        "/(<~([^>]+)>)/", '<a href="/$2">$2</a>', $_);
    $_ = preg_replace(
        "/[\*-] +\[ ?\]/", '- <input todo type=checkbox>', $_);
    $_ = preg_replace(
        "/[\*-] +\[x\]/", '- <input todo type=checkbox checked>', $_);
    $_ = redlinks($_);
    $_ = (new \Michelf\MarkdownExtra)->transform($_);
    $_ = preg_replace("/<li><input todo/", "<li todo><input", $_);
    return $_;
}

