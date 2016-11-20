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
}