<?php
require "vendor/autoload.php";
use Swoole\Http\{Request,Response};

$ip   = $argv[1] ?? "0.0.0.0";
$port = $argv[2] ?? "9001";

$server = new Swoole\HTTP\Server($ip, $port);
$server->on("start", "serve_begin");
$server->on("request", "serve_handle");
$server->start();

function serve_handle(Request $request, Response $response)
{
    [$status, $body, $headers] = route(
        $request->server['request_method'],
        $request->server['request_uri'],
        $request->post,
        $s = wiki_session($request),
    );

    foreach ($headers as $h => $v) {
        $response->header($h, $v);
    }

    if ($status == 302) {
        $response->status($status);
        $response->header("Location", $body);
        $response->end();
        return;
    }

    $response->status($status);
    $response->header("Content-Type", "text/html");
    $response->end($body);

    $post = $request->post ? ' post=' . implode(',', array_keys($request->post)) : '';
    $cookie = $request->cookie ? ' cookie=' . implode(',', array_keys($request->cookie)) : '';
    $session = $s() ? ' sess.count=' . count($s()) : '';
    echo "I " . date('c', $request->server['request_time']) . " " .
         $request->server['request_method'] . " " .
         $request->server['request_uri'] . " $status$post$cookie$session\n";

}

function serve_begin(Swoole\Http\Server $server)
{
    echo sprintf("listening on %s:%s\n", $server->host, $server->port);
}

function wiki_session(Request $r)
{
    if (isset($r->cookie['wiki_session'])) {
        return fn($k = null, $v = null) =>
            wiki_session_storage($r->cookie['wiki_session'], $k, $v);
    }

    return fn($k = null, $v = null) => null;
}

// note: will likely use Swoole\Table later
function wiki_session_storage(int|string $id, $k = null, $v = null)
{
    static $session_store = [];

    if (!isset($session_store[$id])) {
        $session_store[$id] = [];
    }

    if ($k && $v !== null) {
        $session_store[$id][$k] = $v;
    } elseif ($k) {
        return $session_store[$id][$k] ?? null;
    }

    return $session_store[$id];
}
