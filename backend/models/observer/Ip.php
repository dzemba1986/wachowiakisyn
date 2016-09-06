<?php

use backend\models\Ip;

class IpObserver implements SplObserver {
	
	private $ip;
	
	function __construct(Ip $ip) {
		$this->ip = $ip;
	}
}