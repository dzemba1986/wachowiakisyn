<?php

namespace common\models\crm;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property integer $type
 */
class TaskCategory extends ActiveRecord
{
	public static function tableName()
	{
		return '{{task_category}}';
	}

	public function rules()
	{
		return [
			[['name'], 'required'],
			
			[['type'], 'required'],
		];
	}

	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
		];
	}
	
	public static function findWhereType($type){
		
		return self::find()->select(['id', 'name'])->where(['type_id' => $type]);
	}
}
