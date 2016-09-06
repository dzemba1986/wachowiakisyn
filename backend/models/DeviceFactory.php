<?php

namespace backend\models;

class DeviceFactory {
	public static function create($type) {
		switch($type) {
			case Swith::TYPE:
	            return new Swith();
	            break;
            case Router::TYPE:
            	return new Router();
            	break;
	        case GatewayVoip::TYPE:
	            return new GatewayVoip();
	            break;
            case Camera::TYPE:
            	return new Camera();
            	break;
            case MediaConverter::TYPE:
            	return new MediaConverter();
            	break;
            case Server::TYPE:
            	return new Server();
            	break;
            case Virtual::TYPE:
            	return new Virtual();
            	break;
			default : 
				return new Device();
	    }
	}
}
