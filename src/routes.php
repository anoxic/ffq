<?php
const WK = '.well-known/';

function route(string $method, string $uri)
{
    [$code, $body] = match (true) {
        prefix($uri, ':') => handle('edit', $uri),
        prefix($uri, '!') => handle('delete', $uri),
        prefix($uri, '=') => handle('login', $uri),
        prefix($uri, '-') => handle('logout', $uri),
        prefix($uri,  WK) => handle('letsencrypt', $uri),
        default           => handle('view', $uri),
    };

    return [$code, $body];
}

function prefix(string $uri, string $sigil)
{
    return substr($uri, 1, strlen($sigil)) == $sigil;
}

function handle(string $view)
{
    if (file_exists($v = "handlers/$view.php")) {
        ob_start();
        require $v;
        return [$code ?? 200, ob_get_clean()];
    }
    return [404, "<h1 title=\"$v\">404</h1>"];
}
