<?php
require('Page.php');
class DevicePage extends Page {
	function listing() {
		$devices = [];
		foreach($GLOBALS[DS_CONNECT] as $deviceId => $deviceInfo) {
			if (empty($deviceInfo[DEVICE_CONNECT]->getRemoteAddress())) {
				unset($GLOBALS[DS_CONNECT][$deviceId]);
				continue;
			}
			$deviceName = $deviceInfo[DEVICE_NAME];
			$devices[] = [
				"id" => $deviceId,
				"name" => $deviceName,
				"address" => $deviceInfo[DEVICE_CONNECT]->getRemoteAddress(),
				"login_time" => $deviceInfo[LOGIN_AT]
			];
			
		}
		return $this->resJson($devices);
	}
	
	function posting($params) {
		if (!isset($params['id']) || !isset($params['msg'])) {
			return $this->denied("This method not allow");
		}
		
		$deviceId = $params['id'];
		$msg = $params['msg'];
		
		$result = [
			"type" => "error",
			"msg" => "Device $deviceId not found"
		];
		if (isset($GLOBALS[DS_CONNECT][$deviceId])) {
			$deviceInfo = $GLOBALS[DS_CONNECT][$deviceId];
			$deviceName = $deviceInfo[DEVICE_NAME];
			$deviceInfo[DEVICE_CONNECT]->write("Gui tu server (" . $deviceName . "): $msg\n");
			$result = [
				"type" => "success",
				"msg" => "Send data success to $deviceId with msg $msg"
			];
		}
		return $this->resJson($result);
	}
	
	function render($method, $params) {
		if (strtolower($method) == "get") {
			return $this->listing();
		} else {
			return $this->posting($params);
		}
	}
}
?>