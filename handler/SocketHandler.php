<?php


class SocketHandler {

    public static function doLogin($deviceId, \React\Socket\ConnectionInterface $connection, $params) {
        $deviceName = preg_replace('/[^A-Za-z0-9. -]/', '', $params[0]);
        $GLOBALS[DS_CONNECT][$deviceId] = [
            DEVICE_CONNECT => $connection,
            DEVICE_NAME => $deviceName,
            LOGIN_AT => date("Y-m-d H:i:s")
        ];
        echo "\nlogin by $deviceId, device name $deviceName";
        $connection->write("Login OK\n");
    }

    public static function sendingData($deviceId, \React\Socket\ConnectionInterface $connection, $data){
        if (!self::isDeviceExists($deviceId, $connection)) {
            return;
        }
        $device = $GLOBALS[DS_CONNECT][$deviceId];
        $strData = implode(",", $data);
        echo "\nforwarding $strData to $deviceId";
        $device[DEVICE_CONNECT]->write($strData . "\n");
    }

    public static function isDeviceExists($deviceId, \React\Socket\ConnectionInterface $connection) {
        if (!isset($GLOBALS[DS_CONNECT][$deviceId])) {
            echo "\nnot found device $deviceId";
            $connection->write("die\n");
            return false;
        }
        $device = $GLOBALS[DS_CONNECT][$deviceId];
        if (empty($device[DEVICE_CONNECT]->getRemoteAddress())) {
            echo "\ndevice $deviceId was die";
            unset($GLOBALS[DS_CONNECT][$deviceId]);
            $connection->write("die\n");
            return false;
        }
        return true;
    }


}