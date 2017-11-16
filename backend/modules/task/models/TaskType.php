<?php

namespace backend\modules\task\models;

use yii\db\ActiveRecord;
/**
 * @property integer $id
 * @property string $name
 * @property string $color
 * @property integer $type
 */
class TaskType extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return '{{task_type}}';
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
		return array(
			'id' => 'ID',
			'name' => 'Nazwa',
		);
	}
	
	public static function findWhereType($type){
		
		return self::find()->select(['id', 'name'])->where(['type' => $type]);
	}
}
