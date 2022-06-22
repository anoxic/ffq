<?php
const WK = '.well-known/';

function route(string $method, string $uri, ?array $post)
{
    $handler = match (true) {
        prefix($uri, ':') => 'edit',
        prefix($uri, '!') => 'delete',
        prefix($uri, '=') => 'login',
        prefix($uri, '-') => 'logout',
        prefix($uri,  WK) => 'letsencrypt',
        default           => 'view',
    };

    if (file_exists($v = "handlers/$handler.php")) {
        ob_start();
        require $v;
        return [$code ?? 200, ob_get_clean()];
    }
    return [404, "<h1 title=\"$v\">404</h1>"];
}

function prefix(string $uri, string $sigil)
{
    return substr($uri, 1, strlen($sigil)) == $sigil;
}
