<?php

namespace common\models\soa;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 */

class InstallationType extends ActiveRecord
{
	public static function tableName() : string	{
		
		return '{{installation_type}}';
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
