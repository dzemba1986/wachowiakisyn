<?php

namespace rest\versions\v1\controllers;

use yii\rest\ActiveController;
/**
 * Connection Controller API
 *
 * @author Daniel Mikołajewski
 */
class ConnectionController extends ActiveController
{
	public $modelClass = 'backend\models\Connection';
	
	public function actions() {
		$actions = parent::actions();
		unset($actions['delete']);
		
		return $actions;
	}
}