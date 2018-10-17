<?php

namespace common\models\soa;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
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
