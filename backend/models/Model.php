<?php

namespace backend\models;

use backend\models\Device;
use kossmoss\PostgresqlArrayField\PostgresqlArrayFieldBehavior;
/**
 * This is the model class for table "model".
 *
 * The followings are the available columns in table 'model':
 * @property integer $id
 * @property string $name
 * @property integer $port_count
 * @property integer $type
 * @property integer $manufacturer
 * @property array $port

 */
class Model extends \yii\db\ActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return '{{model}}';
	}

	public function behaviors() {
        return [
            [
                'class' => PostgresqlArrayFieldBehavior::className(),
                'arrayFieldName' => 'port', // model's field to attach behavior
                'onEmptySaveNull' => true // if set to false, empty array will be saved as empty PostreSQL array '{}' (default: true)
            ]    
        ];
    }
	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			[['name', 'port_count', 'type', 'manufacturer', 'port'], 'required'],
			[['name', 'port_count', 'type', 'manufacturer', 'port'], 'safe'],
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
    
    public function getMOdelDevice(){
	
		//Wiele urzadzeń danego modelu
		return $this->hasMany(Device::className(), ['model'=>'id']);
	}
}
