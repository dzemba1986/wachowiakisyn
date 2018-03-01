<?php

namespace backend\models;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property array $installation_type
 */

class ConnectionType extends ActiveRecord
{
	public static function tableName() : string	{
		
		return '{{connection_type}}';
	}
	
	public function rules() : array	{
		
		return [
		    ['name', 'string'],
			['name', 'required'],
		    
			['name', 'safe'],
		];
	}
	
	public function attributeLabels() : array {
		
		return [
			'name' => 'Nazwa',
		];
	}
}
