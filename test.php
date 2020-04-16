<?php


use Yd\data;

require 'vendor/autoload.php';

$server = new Swoole\WebSocket\Server("0.0.0.0", 9501);
$server->protobuf = new data();

$server->on('open', function (Swoole\WebSocket\Server $server, $request) {
    echo "server: handshake success with fd{$request->fd}\n";
});

$server->on('message', function (Swoole\WebSocket\Server $server, $frame) {

    $server->protobuf->mergeFromString($frame->data);
    $y =  $server->protobuf->getY();
    $d =  $server->protobuf->getD();

    echo $y, $d, PHP_EOL;

    $server->protobuf->setY($y);
    $server->protobuf->setD($d);
    $ret = $server->protobuf->serializeToString();

    $server->push($frame->fd, $ret, WEBSOCKET_OPCODE_BINARY);
});

$server->on('close', function ($ser, $fd) {
    echo "client {$fd} closed\n";
});

$server->start();