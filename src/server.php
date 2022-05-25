<?php
require "vendor/autoload.php";

const IP   = "0.0.0.0";
const PORT = "9001";

$server = new Swoole\HTTP\Server(IP, PORT);
$server->on("start", "serve_begin");
$server->on("request", "serve_handle");
$server->start();

function serve_handle(
    Swoole\Http\Request  $request,
    Swoole\Http\Response $response,
)
{
    [$status, $body] = route(
        $request->server['request_method'],
        $request->server['request_uri'],
    );
    $response->status($status);
    $response->header("Content-Type", "text/html");
    $response->end($body);
}

function serve_begin(Swoole\Http\Server $server)
{
    echo sprintf("listing on %s:%s\n", IP, PORT);
}

