<?php

namespace backend\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "address_short".
 *
 * The followings are the available columns in table 'address_short':
 * @property integer $id
 * @property string t_woj
 * @property string t_pow
 * @property string t_gmi
 * @property string t_miasto
 * @property string t_ulica
 * @property string ulica_prefix
 * @property string ulica
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
	 * 
	 * {@inheritDoc}
	 * @see \yii\base\Model::rules()
	 */
	public function rules() : array {
		
		return [
			['t_woj', 'string', 'min' => 1, 'max' => 2],
			['t_woj', 'default', 'value' => '30'],
			['t_woj', 'trim'],
				
			['t_pow', 'string', 'min' => 1, 'max' => 2],
			['t_pow', 'default', 'value' => '64'],
			['t_pow', 'trim'],
			
			['t_gmi', 'string', 'min' => 1, 'max' => 2],
			['t_gmi', 'trim'],
			
			['t_rodz', 'string', 'min' => 1, 'max' => 1],
			['t_rodz', 'default', 'value' => '9'],
			['t_rodz', 'trim'],
			
			['t_miasto', 'string', 'min' => 7, 'max' => 7],
			['t_miasto', 'trim'],
			
			['t_ulica', 'string', 'min' => 5, 'max' => 7],
			[['t_ulica', 't_miasto'], 'unique', 'targetAttribute' => ['t_ulica', 't_miasto'], 'message' => 'Ulica istnieje'],	//TODO jeżeli wyjdziemy poza Poznań należy klucz unikalny nałożyć na `t_ulica` + `t_miasto` 
			
			['ulica_prefix', 'string', 'min' => 1, 'max' => 3],
			['ulica_prefix', 'trim'],
			
			['ulica', 'string', 'min' => 2, 'max' => 255],
			['ulica', 'required', 'message' => 'Wartość wymagana'],
			['ulica', 'trim'],
			
			[['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 'ulica_prefix', 'ulica', 'name'], 'safe'],
		];
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() : array {
		
		return [
			'id' => 'ID',
			't_miasto' => 'Teryt miasto',
			't_woj' => 'Teryt województwo',
			't_pow' => 'Teryt powiat',
			't_gmi' => 'Teryt gmina',
			't_rodz' => 'Teryt rodzaj',
			't_ulica' => 'Teryt ulica',	
			'ulica_prefix' => 'prefix',	
			'name' => 'Skrót',
			'ulica' => 'Ulica',	
			'config' => 'Konfiguracja'
		];
	}
}
