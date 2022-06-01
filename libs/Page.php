<?php
class Page {
	function resJson($obj) {
		return new React\Http\Response(
			200,
			array(
				'Content-Type' => 'application/json'
			),
			json_encode($obj)
		);
	}
	
	function denied($msg) {
		return $this->resJson([
			"type" => "denied",
			"msg" => $msg
		]);
	}
}