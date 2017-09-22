<?php

namespace backend\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "address_short".
 *
 * The followings are the available columns in table 'address_short':
 * @property integer $id
 * @property string $name
 * @property integer $config
 */

class AddressShort extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName() : string {
		
		return '{{address_short}}';
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() : array {
		
		return [
			'id' => 'ID',
			'name' => 'Nazwa',
		];
	}
}
