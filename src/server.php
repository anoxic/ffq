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
    echo "[" . date('c', $request->server['request_time']) . "] " .
         $request->server['request_method'] . " " .
         $request->server['request_uri'] . "\n";

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
}

function serve_begin(Swoole\Http\Server $server)
{
    echo sprintf("listening on %s:%s\n", IP, PORT);
}

