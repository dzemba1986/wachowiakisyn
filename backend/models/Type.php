<?php

namespace backend\models;

use backend\models\Installation;
use backend\models\Connection;
use backend\models\Package;
use yii\db\ActiveRecord;
use yii\db\ActiveQueryInterface;
/**
 * This is the model class for table "connection_type".
 *
 * The followings are the available columns in table 'connection_type':
 * @property integer $id
 * @property string $name

 */

//TODO klasa wymaga zmiany nazwy na 'ConnectionType'
class Type extends ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName() : string	{
		
		return '{{connection_type}}';
	}
	
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules() : array	{
		
		return [
				[['name'], 'required'],
				[['name'], 'safe'],
		];
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
	
	//TODO ta funkcja jest do usunięcia, sprawdzić czy gdzieś nie jest wykorzystywana
	public function getInstallations() {
	
		//Wiele instalacji na danym adresie
		return $this->hasMany(Installation::className(), ['type'=>'id']);
	}
	
	/**
	 * 
	 * @return \yii\db\ActiveQuery
	 */
	public function getConnections() : ActiveQueryInterface {
	
		//wiele umów danego typu
		return $this->hasMany(Connection::className(), ['type'=>'id']);
	}
	
	/**
	 * 
	 * @return \yii\db\ActiveQuery
	 */
	public function getPackages() : ActiveQueryInterface {
	
		//wiele pakietów w danym type umowy
		return $this->hasMany(Package::className(), ['type'=>'id']);
	}
}
