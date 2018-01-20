<?php

namespace backend\models;

use backend\modules\task\models\InstallTask;
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
 * @property array $installations
 * @property array $devices
 * @property array $tasks
 * @property array $connections
 */
class Address extends ActiveRecord
{
	const SCENARIO_UPDATE = 'update';
	
	public static function tableName() : string {
		
		return '{{address}}';
	}
	
	public function rules() : array {
		
		return [
			['t_ulica', 'required', 'message' => 'Wartość wymagana'],
				
			['dom', 'string', 'min' => 1, 'max' => 10],
			['dom', 'required', 'message' => 'Wartość wymagana'],
			['dom', 'trim'],				
				
			['dom_szczegol', 'string', 'min' => 1, 'max' => 50],
			['dom_szczegol', 'default', 'value' => ''],
			['dom_szczegol', 'filter', 'filter' => 'strtoupper'],
			['dom_szczegol', 'trim'],
			
			['lokal', 'string', 'min' => 1, 'max' => 10],
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
		//TODO jeżeli WTVK wyjdzie poza Poznań, trzeba to uaktualnić
		$scenarios[self::SCENARIO_UPDATE] = ['t_miasto', 't_ulica', 't_gmi', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'];
	
		return $scenarios;
	}
	
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
	
	public function beforeSave($insert){
		
		if ($insert){
			$shortAddress = AddressShort::findOne(['t_ulica' => $this->t_ulica]);
			
			$this->t_miasto = $shortAddress->t_miasto;
			$this->t_woj = $shortAddress->t_woj;
			$this->t_pow = $shortAddress->t_pow;
			$this->t_gmi = $shortAddress->t_gmi;
			$this->t_rodz = $shortAddress->t_rodz;
			$this->ulica_prefix = $shortAddress->ulica_prefix;
			$this->ulica = $shortAddress->ulica;
			
			if (!$this->pietro)
				$this->pietro = '';
		}
		
		if (!parent::beforeSave($insert)){
			return false;
		}
		
		return true;
	}
	
	public function save($runValidation = true, $attributeNames = null){
		
		if ($this->getIsNewRecord()) {
			
			if ($this->validate() && !is_object($existAddress = $this->exist())){
				return $this->insert($runValidation, $attributeNames);
			}
			else {
				$this->setIsNewRecord(false);
				$this->id = $existAddress->id;
				
				return true;
			}
		} else {
			return $this->update($runValidation, $attributeNames) !== false;
		}
	}
	
	public function toString($short = false){
		
		if ($short){
			if (empty($this->pietro))
				if ($this->lokal)
					return $this->getShortAddress()->name . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol;
				else
					return $this->getShortAddress()->name . $this->dom . $this->dom_szczegol;
			else
				if ($this->lokal)
					return $this->getShortAddress()->name . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol . ' (piętro ' . $this->pietro . ')';
				else
					return $this->getShortAddress()->name . $this->dom . $this->dom_szczegol . ' (piętro ' . $this->pietro . ')';
				
		} else {
			if (empty($this->pietro))
				if ($this->lokal)
					return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol;
				else
					return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol;
			else
				if ($this->lokal)
					return $this->ulica_prefix . ' '.$this->ulica . ' ' . $this->dom . $this->dom_szczegol . '/'.$this->lokal . $this->lokal_szczegol . ' (piętro ' . $this->pietro . ')';
				else
					return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol . ' (piętro ' . $this->pietro . ')';
		}
	}
	
	public static function getFloor() : array {
		
		for ($i = -2; $i <= 16; $i++) {
			$array[$i] = $i;
		}
	
		return $array;
	}
	
	public function getConfigMode(){
		
		return $this->getShortAddress()->config;
	}
	
	public function getInstallations(){
	
		return $this->hasMany(Installation::className(), ['address'=>'id']);
	}
	
	public function getConnections(){
	
		return $this->hasMany(Connection::className(), ['address'=>'id']);
	}
	
	public function getDevices(){
	
		return $this->hasMany(Device::className(), ['address'=>'id']);
	}
	
	public function getTasks(){
	
		return $this->hasMany(InstallTask::className(), ['address'=>'id']);
	}
	
	private function getShortAddress(){
		
		return $this->hasOne(AddressShort::className(), ['t_ulica'=>'t_ulica'])->one();
	}

	private function exist(){
	
		return  self::find()->where(['t_ulica' => $this->t_ulica, 
			'dom' => $this->dom, 
			'dom_szczegol' => $this->dom_szczegol, 
			'lokal' => $this->lokal,
			'pietro' => is_null($this->pietro) ? '' : $this->pietro
		])->one();
	}
}
