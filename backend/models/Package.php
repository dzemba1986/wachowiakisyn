<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use app\models\connection\Connection;
/**
 * This is the model class for table "tbl_package".
 *
 * The followings are the available columns in table 'tbl_package':
 * @property integer $id
 * @property string $name
 * @property intiger $type
 * @property string $download
 * @property string $upload

 */
class Package extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return '{{package}}';
	}

	/**
	 * @return Connection object array for same address
	 */
	public function getConnections(){
	
		//Wiele umów na danym adresie
		return $this->hasMany(Connection::className(), ['package'=>'id']);
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			[['name', 'type'], 'required'],
			[['name', 'type', 'download', 'upload'], 'safe'],
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
			'type' => 'Typ',
			'download' => 'Download',
			'upload' => 'Upload',
		);
	}
}
