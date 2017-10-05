<?php

namespace backend\models;

use backend\models\Installation;
use yii\db\ActiveQueryInterface;
use yii\db\ActiveRecord;
/**
 * This is the model class for table 'address'.
 *
 * The followings are the available columns in table 'address':
 * @property integer $id PK
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
 * @property string $shortAddress 
 * 
 */
class Address extends ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	const SCENARIO_DELETE = 'delete';
	/**
	 * @return string the associated database table name
	 */
	public static function tableName() : string {
		
		return '{{address}}';
	}
	
	public function rules() : array {
		
		return [
				
			//pola będą wypełniane automatycznie, nie są potrzebne reguły
			//['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 'ulica_prefix', 'ulica']
			
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
				
			[['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'], 'safe'],
		];
	}
	
	public function scenarios() : array {
	
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 
			'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'
		];
		$scenarios[self::SCENARIO_UPDATE] = ['t_ulica', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'];
	
		return $scenarios;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels() : array {
		
		return [
			'id' => 'ID',
			'ulica_prefix' => 'Prefix',
			'dom' => 'Blok',
			'dom_szczegol' => 'Klatka',
			'lokal' => 'Lokal',
			'lokal_szczegol' => 'Lokal szczegół',
			'pietro' => 'Piętro'
		];
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
	 * @return string
	 */
	public function __toString() : string {
		
		if (empty($this->pietro))
			if ($this->lokal)
				return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . strtoupper($this->dom_szczegol) . '/' . $this->lokal . $this->lokal_szczegol;
			else
				return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . strtoupper($this->dom_szczegol);
		else
			if ($this->lokal)
				return $this->ulica_prefix . ' '.$this->ulica . ' ' . $this->dom . strtoupper($this->dom_szczegol) . '/'.$this->lokal . $this->lokal_szczegol . ' (piętro ' . $this->pietro . ')';
			else
				return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . strtoupper($this->dom_szczegol) . ' (piętro ' . $this->pietro . ')';
	}
	
	/**
	 * @return string full name address 
	 */
	//TODO remove this function if not use
	public function getFullAddress(){
		
		return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol;
	}
	
	//TODO remove this function if not use
	public function getShortAddress(){
	
		return $this->modelShortStreet->name . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol;
	}
	
	/**
	 * @return array of floor number
	 */
	public static function getFloor() : array {
	
		return ['-1' => '-1', '0' => '0', '1' => '1', '2' => '2', '3' => '3', '6' => '6', '7' => '7', '8' => '8', '9' => '9', '11' => '11'];
	}
	
	//TODO remove this function if not use
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
	
	//TODO rename function to "getShortAddress"
	/**
	 * @return string
	 */
	public function getFullDeviceShortAddress() : string {
	
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
	
	/**
	 * @return ActiceQueryInterface the relational query object
	 */
	public function getInstallations() : ActiveQueryInterface {
	
		//Wiele instalacji na danym adresie
		return $this->hasMany(Installation::className(), ['address'=>'id']);
	}
	
	/**
	 * @return ActiceQueryInterface the relational query object
	 */
	public function getConnections(){
	
		//Wiele umów na danym adresie
		return $this->hasMany(Connection::className(), ['address'=>'id']);
	}
	
	/**
	 * @return ActiceQueryInterface the relational query object
	 */
	public function getModelsDevice(){
	
		//Wiele urządzeń na danym adresie
		return $this->hasMany(Device::className(), ['address'=>'id']);
	}
	
	/**
	 * @return ActiceQueryInterface the relational query object
	 */
	public function getModelsTask(){
	
		//Wiele zadań na danym adresie
		return $this->hasMany(Task::className(), ['address'=>'id']);
	}
	
	/**
	 * @return ActiceQueryInterface the relational query object
	 */
	public function getModelShortStreet(){
	
		//Powiązanie dla krótkich adresów
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
