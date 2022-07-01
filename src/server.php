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
    echo "I " . date('c', $request->server['request_time']) . " " .
         $request->server['request_method'] . " " .
         $request->server['request_uri'] . " $status$post$cookie\n";

}

function serve_begin(Swoole\Http\Server $server)
{
    echo sprintf("listening on %s:%s\n", $server->host, $server->port);
}

