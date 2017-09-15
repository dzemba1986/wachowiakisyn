<?php

namespace backend\models;

use Yii;
use backend\models\Installation;
/**
 * This is the model class for table "tbl_address".
 *
 * The followings are the available columns in table 'tbl_localization':
 * @property integer $id
 * @property string $t_woj
 * @property string $t_pow
 * @property string $t_gmi
 * @property string $t_rodz
 * @property string $t_miasto
 * @property string $t_ulica
 * @property string $ulica_prefix
 * @property string $ulica
 * @property string $dom
 * @property string $dom_szczegol
 * @property string $lokal
 * @property string $lokal_szczegol
 * @property string $pietro
 */
class Address extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_DELETE = 'delete';
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return '{{address}}';
	}
	
	public function rules()
	{
		return [
			['t_woj', 'string', 'min' => 1, 'max' => 2],
			['t_woj', 'default', 'value' => '30'],
			['t_woj', 'trim'],				
				
			['t_pow', 'string', 'min' => 1, 'max' => 2],
			['t_pow', 'default', 'value' => '64'],
			['t_pow', 'trim'],
				
			['t_gmi', 'string', 'min' => 1, 'max' => 2],
			['t_gmi', 'default', 'value' => '05'],
			['t_gmi', 'trim'],
				
			['t_rodz', 'string', 'min' => 1, 'max' => 1],
			['t_rodz', 'default', 'value' => '9'],
			['t_rodz', 'trim'],
				
			['t_miasto', 'string', 'min' => 7, 'max' => 7],
			['t_miasto', 'default', 'value' => '0970224'],
			['t_miasto', 'trim'],
				
			['t_ulica', 'string', 'min' => 7, 'max' => 7],
			['t_ulica', 'default', 'value' => function (){ return $this->getTUlica(); }],
			//['t_ulica', 'trim'],
			
			['ulica_prefix', 'string', 'min' => 1, 'max' => 3],
			['ulica_prefix', 'default', 'value' => function (){ return $this->getUlicaPrefix(); }],
			['ulica_prefix', 'trim'],
				
			['ulica', 'string', 'min' => 2, 'max' => 255],
			['ulica', 'required', 'message' => 'Wartość wymagana'],
			['ulica', 'trim'],	
				
			['dom', 'string', 'min' => 1, 'max' => 20],
			['dom', 'required', 'message' => 'Wartość wymagana'],
			['dom', 'trim'],				
				
			['dom_szczegol', 'string', 'min' => 1, 'max' => 50],
			['dom_szczegol', 'default', 'value' => ''],
			['dom_szczegol', 'filter', 'filter'=>'strtoupper'],
			['dom_szczegol', 'trim'],
			
			['lokal', 'string', 'min' => 1, 'max' => 20],
			['lokal', 'default', 'value' => ''],
			['lokal', 'trim'],
			
			['lokal_szczegol', 'string', 'min' => 1, 'max' => 50],
			['lokal_szczegol', 'default', 'value' => ''],
			['lokal_szczegol', 'filter', 'filter'=>'strtoupper'],
			['lokal_szczegol', 'trim'],
			
			['pietro', 'string', 'min' => -1, 'max' => 2],
			['pietro', 'default', 'value' => ''],
				
			[['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 'ulica_prefix',
				'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'], 'safe'],
		];
	}
	
	public function scenarios(){
	
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 
			'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'];
		$scenarios[self::SCENARIO_UPDATE] = ['t_ulica', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'];
	
		return $scenarios;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
				'id' => 'ID',
				'ulica_prefix' => 'Prefix',
				'dom' => 'Blok',
				'dom_szczegol' => 'Klatka',
				'lokal' => 'Lokal',
				'lokal_szczegol' => 'Lokal szczegół',
				'pietro' => 'Piętro'
		);
	}
	
	public function save($runValidation = true, $attributeNames = null){
		
		if ($this->getIsNewRecord()) {
			if ($this->validate() && !$this->exist()){
				return $this->insert($runValidation, $attributeNames);
			}
			else {
				$this->setIsNewRecord(false);
				$this->id = $this->getModelExist()->id;
				
				return true;
			}	
		} else {
			return $this->update($runValidation, $attributeNames) !== false;
		}
	}
	
	/**
	 * @return string full name address
	 */
	public function getFullAddress(){
		
		return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol;
	}
	
	public function getShortAddress(){
	
		return $this->modelShortStreet->name . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol;
	}
	
	public static function getFloor(){
	
		return ['-1' => '-1', '0' => '0', '1' => '1', '2' => '2', '3' => '3', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '11' => '11'];
	}
	
	public function getFullDeviceAddress(){
	
		//var_dump($this->pietro); exit();
		
		if (!empty($this->pietro))
			if ($this->lokal)
				return $this->ulica_prefix . ' '.$this->ulica . ' ' . $this->dom . strtoupper($this->dom_szczegol) . '/'.$this->lokal . $this->lokal_szczegol . ' (piętro ' . $this->pietro . ')';
			else 
				return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . strtoupper($this->dom_szczegol) . ' (piętro ' . $this->pietro . ')';
		else 
			if ($this->lokal)
				return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . strtoupper($this->dom_szczegol) . '/' . $this->lokal . $this->lokal_szczegol;
			else 
				return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . strtoupper($this->dom_szczegol);				
	}
	
	public function getFullDeviceShortAddress(){
	
		if (!empty($this->pietro))
			if ($this->lokal)
				return $this->modelShortStreet->name . $this->dom . strtoupper($this->dom_szczegol) . '/' . $this->lokal . $this->lokal_szczegol . ' (piętro ' . $this->pietro . ')';
			else
				return $this->modelShortStreet->name . $this->dom . strtoupper($this->dom_szczegol) . ' (piętro ' . $this->pietro . ')';
		else
			if ($this->lokal)
				return $this->modelShortStreet->name . $this->dom . strtoupper($this->dom_szczegol) . '/' . $this->lokal . $this->lokal_szczegol;
			else
				return $this->modelShortStreet->name . $this->dom . strtoupper($this->dom_szczegol);
	}

	public function getInstallations(){
	
		//Wiele instalacji na danym adresie
		return $this->hasMany(Installation::className(), ['address'=>'id']);
	}
	/**
	 * @return Connection object array for same address
	 */
	public function getConnections(){
	
		//Wiele umów na danym adresie
		return $this->hasMany(Connection::className(), ['address'=>'id']);
	}
	
	public function getModelsDevice(){
	
		//Wiele instalacji na danym adresie
		return $this->hasMany(Device::className(), ['address'=>'id']);
	}
	
	public function getModelsTask(){
	
		//Wiele instalacji na danym adresie
		return $this->hasMany(Task::className(), ['address'=>'id']);
	}
	
	public function getModelShortStreet(){
	
		//Wiele umów na danym adresie
		return $this->hasOne(AddressShort::className(), ['t_ulica'=>'t_ulica']);
	}
	
	private function getTUlica(){
		
		return self::find()->select('t_ulica')->where(['ulica' => $this->ulica])->one()->t_ulica;
	}
	
	private function getUlicaPrefix(){
	
		return self::find()->select('ulica_prefix')->where(['ulica' => $this->ulica])->one()->ulica_prefix;
	}

	private function exist(){
	
		return  Address::find()->where(['ulica' => $this->ulica, 
			'dom' => $this->dom , 
			'dom_szczegol' => $this->dom_szczegol, 
			'lokal' => $this->lokal,
			'pietro' => $this->pietro	
		])->exists();
	}
	
	private function getModelExist() {
		
		return Address::find()->where(['ulica' => $this->ulica, 
			'dom' => $this->dom , 
			'dom_szczegol' => $this->dom_szczegol, 
			'lokal' => $this->lokal,
			'pietro' => $this->pietro
		])->one();
	}
}
