<?php
if (!isset($loop)) {
    $loop = React\EventLoop\Factory::create();
}

if (!isset($httpURI)) {
    $httpURI = '0.0.0.0:' . HTTP_PORT;
}

$httpSocket = new React\Socket\Server($httpURI, $loop);

$server = new React\Http\Server(function (Psr\Http\Message\ServerRequestInterface $request) {
    $params = $request->getQueryParams();
    if (!isset($params['id']) || !isset($params['msg'])) {
        $body = "";
        $body .= '<!DOCTYPE html>';
        $body .= '<html lang="en">';
        $body .= '<head>';
        $body .= '    <title>Bootstrap Example</title>';
        $body .= '    <meta charset="utf-8">';
        $body .= '    <meta name="viewport" content="width=device-width, initial-scale=1">';
        $body .= '    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">';
        $body .= '    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">';
        $body .= '    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>';
        $body .= '    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>';
        $body .= '    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>';
        $body .= '    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>';
        $body .= '    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" >';
        $body .= '</head>';
        $body .= '<body>';
        $body .= '<div class="container">';
        $body .= '<table class="table">';
        $body .= '<thead><tr><th>Device ID</th><th>Device Name</th><th>TCP/IP</th><th>Action</th></tr></thead><tbody>';
        foreach($GLOBALS[DS_CONNECT] as $deviceId => $deviceInfo) {
            if (empty($deviceInfo[DEVICE_CONNECT]->getRemoteAddress())) {
                unset($GLOBALS[DS_CONNECT][$deviceId]);
                continue;
            }
            $deviceName = $deviceInfo[DEVICE_NAME];
            $body .= "<tr>";
            $body .= "<td>$deviceId</td>";
            $body .= "<td>$deviceName</td>";
            $body .= "<td>" . $deviceInfo[DEVICE_CONNECT]->getRemoteAddress() . "</td>";
            $body .= '<td><form class="form-inline" method="get" action="/tcp/"> <input type="hidden" name="id" value="' . $deviceId . '" /> ';
            $body .= '<div class="form-group"> <label for="msg">Send msg to [' . $deviceName . ']:</label> <input type="text" class="form-control" name="msg" id="msg"> </div> ';
            $body .= '<button type="submit" class="btn btn-success">Send</button> </form></td>';
            $body .= "</tr>";

        }
        $body .= "</tbody></table>";
        $body .= '</div>';
        $body .= '</body>';
        $body .= '</html>';
        return new React\Http\Response(
            200,
            array(
                'Content-Type' => 'text/html'
            ),
            $body
        );
    }

    $deviceId = $params['id'];
    $msg = $params['msg'];
    if (!isset($GLOBALS[DS_CONNECT][$deviceId])) {
        return new React\Http\Response(
            200,
            array(
                'Content-Type' => 'text/plain'
            ),
            "Device $deviceId not found"
        );
    }
    $deviceInfo = $GLOBALS[DS_CONNECT][$deviceId];
    $deviceInfo[DEVICE_CONNECT]->write("$msg\n");
    return new React\Http\Response(
        200,
        array(
            'Content-Type' => 'text/plain'
        ),
        "Send data success to $deviceId with msg $msg"
    );

});
$server->listen($httpSocket);

echo "\nhttp server at http://$httpURI";

?>