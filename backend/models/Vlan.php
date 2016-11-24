<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "vlan".
 *
 * The followings are the available columns in table 'vlan':
 * @property integer $id
 * @property string $desc
 */
class Vlan extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'vlan';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['id', 'required', 'message' => 'Wartość wymagana'],
			['id', 'integer', 'min' => 1, 'max' => 4096, 'tooSmall' => 'Wartość za mała', 'tooBig' => 'Wartość za duża', 'message' => 'Wartość liczbowa'],	
				
			['desc', 'required', 'message' => 'Wartość wymagana'],
			['desc', 'string'],

			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			[['id', 'desc'], 'safe'],
		];
	}
	
	public function scenarios(){
	
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['id', 'desc'];
		$scenarios[self::SCENARIO_UPDATE] = ['desc'];
	
		return $scenarios;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'Vlan',
			'desc' => 'Opis',
		);
	}
	
	public function getModelSubnets(){
	
		return $this->hasMany(Subnet::className(), ['vlan'=> 'id']);
	}
}
