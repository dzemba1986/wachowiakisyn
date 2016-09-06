<?php

namespace backend\models;

use yii\db\ActiveRecord;
/**
 * This is the model class for table "tbl_mod_category".
 *
 * The followings are the available columns in table 'tbl_localization':
 * @property integer $id
 * @property string $name
 */
class TaskCategory extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'task_category';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			[['name'], 'required'],
			[['id', 'name'], 'safe'],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
		];
	}
}
