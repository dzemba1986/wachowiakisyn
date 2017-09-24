<?php

namespace backend\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "vlan".
 *
 * The followings are the available columns in table 'vlan':
 * @property integer $id
 * @property string $desc
 * @property ActiveQuery $modelSubnets
 */

class Vlan extends ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	
	/**
	 * {@inheritdoc}
	 * @return string
	 */
	public static function tableName() : string {
		
		return '{{vlan}}';
	}

	/**
	 * 
	 * {@inheritDoc}
	 * @see \yii\base\Model::rules()
	 */
	public function rules() : array {
		
		return [
			['id', 'required', 'message' => 'Wartość wymagana'],
			['id', 'integer', 'min' => 1, 'max' => 4096, 'tooSmall' => 'Wartość za mała', 'tooBig' => 'Wartość za duża', 'message' => 'Wartość liczbowa'],	
				
			['desc', 'required', 'message' => 'Wartość wymagana'],
			['desc', 'string'],

			[['id', 'desc'], 'safe'],
		];
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \yii\base\Model::scenarios()
	 */
	public function scenarios() : array {
	
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['id', 'desc'];
		$scenarios[self::SCENARIO_UPDATE] = ['desc'];
	
		return $scenarios;
	}
	
	/**
	 * 
	 * {@inheritDoc}
	 * @see \yii\base\Model::attributeLabels()
	 */
	public function attributeLabels()
	{
		return [
			'id' => 'Vlan',
			'desc' => 'Opis',
		];
	}
	
	/**
	 * 
	 * @return ActiveQuery
	 */
	public function getModelSubnets() : ActiveQueryInterface {
	
		//do vlanu należy wiele podsieci
		return $this->hasMany(Subnet::className(), ['vlan'=> 'id']);
	}
}
