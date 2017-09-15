<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use backend\models\Installation;
use backend\models\Connection;
use backend\models\Package;
/**
 * This is the model class for table "connection_type".
 *
 * The followings are the available columns in table 'connection_type':
 * @property integer $id
 * @property string $name

 */
class Type extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return '{{connection_type}}';
	}

	public function getInstallations(){
	
		//Wiele instalacji na danym adresie
		return $this->hasMany(Installation::className(), ['type'=>'id']);
	}
	/**
	 * @return Connection object array for same address
	 */
	public function getConnections(){
	
		//Wiele umów na danym adresie
		return $this->hasMany(Connection::className(), ['type'=>'id']);
	}
	
	public function getPackages(){
	
		//Wiele umów na danym adresie
		return $this->hasMany(Package::className(), ['type'=>'id']);
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
			[['name'], 'safe'],
		];
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
