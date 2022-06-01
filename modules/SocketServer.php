<?php
if (!isset($loop)) {
    $loop = React\EventLoop\Factory::create();
}
if (!isset($socketURI)) {
    $socketURI = '0.0.0.0:' . SOCKET_PORT;
}

$socket = new React\Socket\Server($socketURI, $loop);

$socket->on('connection', function (React\Socket\ConnectionInterface $connection) {
    echo "\nReceive client connect from : " . $connection->getRemoteAddress();
    $connection->on('data', function ($data) use ($connection) {
        if (DataUtils::invalid($data)) {
            return;
        }

        $data = trim(preg_replace('/\s\s+/', ' ', $data));

        $obj = explode(",", $data);
        if (CollectionUtils::lessThanByItem($obj, 3)) {
            echo "\nlength of items not valid";
            return;
        }

        $auth = trim($obj[0]);
        $type = trim($obj[1]);
        $deviceId = trim($obj[2]);

        if (empty($type) || empty($deviceId) || empty($auth)) {
            echo "\nparams not valid";
            return;
        }

        if ($auth !== AUTH_STR) {
            echo "\nauthen fail because not match with " . AUTH_STR;
            return;
        }

        switch ($type) {
            case "LOGIN":
                echo "\ndevice $deviceId in logging";
                $params = array_slice($obj, 3);
                SocketHandler::doLogin($deviceId, $connection, $params);
                break;
            case "DATA":
                $destDeviceId = trim($obj[3]);
                if (empty($destDeviceId)) {
                    echo "\n dest device ID not valid";
                    break;
                }
                echo "\ndevice $deviceId in forwarding data to $destDeviceId";
                $params = array_slice($obj, 4);
                SocketHandler::sendingData($destDeviceId, $connection, $params);
                break;
            case "PING":
                $destDeviceId = trim($obj[3]);
                echo "\ndevice $deviceId in pinging $destDeviceId";
                if (SocketHandler::isDeviceExists($destDeviceId, $connection)) {
                    $connection->write("OK\n");
                    echo "\ndevice $destDeviceId alive";
                }
                echo "\ndevice $destDeviceId died";
                break;
        }

    });

    $connection->on('error', function (Exception $e) {
        echo '\nerror: ' . $e->getMessage();
    });

    $connection->on('close', function () {
        echo "\nClose connect";
    });
});

echo "\nsocket server at http://$socketURI";

?>