<?php
const WK = '.well-known/';

function route(string $method, string $uri, ?array $post)
{
    $handler = match (true) {
        prefix($uri, ':') => 'edit',
        prefix($uri, '!') => 'delete',
        prefix($uri, '=') => 'login',
        prefix($uri, '-') => 'logout',
        prefix($uri, '*') => 'all',
        prefix($uri,  WK) => 'letsencrypt',
        default           => 'view',
    };

    if (file_exists($v = "handlers/$handler.php")) {
        ob_start();
        require $v;
        $body = ob_get_clean();
        if (!empty($redirect)) {
            return [302, $redirect];
        } else {
            return [$code ?? 200, $body];
        }
    }
    return [404, "<h1 title=\"$v\">404</h1>"];
}

function prefix(string $uri, string $sigil)
{
    return substr($uri, 1, strlen($sigil)) == $sigil;
}
