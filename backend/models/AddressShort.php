<?php

namespace backend\models;

/**
 * This is the model class for table "address_short".
 *
 * The followings are the available columns in table 'address_short':
 * @property integer $id
 * @property string $name
 * @property integer $config
 */

class AddressShort extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return '{{address_short}}';
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'name' => 'Nazwa',
		);
	}
}
