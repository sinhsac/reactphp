<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Asia/Ho_Chi_Minh');
require __DIR__ . '/vendor/autoload.php';

include './config.php';
include './helper/DataUtils.php';
include './helper/CollectionUtils.php';
include './handler/SocketHandler.php';

$loop = React\EventLoop\Factory::create();

echo "Server da start\n";
$GLOBALS[DS_CONNECT] = [];

$httpURI = '0.0.0.0:' . HTTP_PORT;
$socketURI = '0.0.0.0:' . SOCKET_PORT;

include_once './modules/HttpServer.php';
include_once './modules/SocketServer.php';

echo "\n\n";

$loop->run();
