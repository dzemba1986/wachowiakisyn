<?php

namespace common\models\seu;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 */

class Manufacturer extends ActiveRecord
{
	public static function tableName()
	{
		return '{{manufacturer}}';
	}

	public function rules()
	{
		return [
			[['name'], 'required'],
			[['name'], 'safe'],
		];
	}
	
	public function attributeLabels()
	{
		return [
			'name' => 'Nazwa',
		];
	}
}