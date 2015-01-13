<?php
// markdown compiles and extend markdown into html
function markdown($_) {
    $_ = preg_replace(
        "/(<~([^>]+)>)/", '<a href="/$2">$2</a>', $_);
    $_ = preg_replace(
        "/[\*-] +\[ ?\]/", '- <input type=checkbox disabled>', $_);
    $_ = preg_replace(
        "/[\*-] +\[x\]/", '- <input type=checkbox checked disabled>', $_);
    $_ = redlinks($_);

    return (new \Michelf\MarkdownExtra)->transform($_);
}

