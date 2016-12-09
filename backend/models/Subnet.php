<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "subnet".
 *
 * The followings are the available columns in table 'subnet':
 * @property integer $id
 * @property string $ip
 * @property string $desc
 * @property integer $vlan
 * @property integer $dhcp_group
 */
class Subnet extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	
	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'subnet';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['ip', 'required', 'message' => 'Wartość wymagana'],
			['ip', 'ip', 'subnet' => true, 'ipv6' => false, 'message' => 'Zły format adresu ip', 'wrongCidr' => 'Niewłasciwy prefix', 'noSubnet' => 'Brak prefiksu'],
				
			['desc', 'required', 'message' => 'Wartość wymagana'],
			['desc', 'string'],
				
			['vlan', 'required', 'message' => 'Wartość wymagana'],
			['vlan', 'integer', 'min' => 1, 'max' => 4096, 'tooSmall' => 'Wartość za mała', 'tooBig' => 'Wartość za duża', 'message' => 'Wartość liczbowa'],
				
// 			['dhcp_group', 'required', 'message' => 'Wartość wymagana'],
			['dhcp_group', 'integer', 'message' => 'Wartość liczbowa'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			[['id', 'ip', 'desc', 'device'], 'safe'],
		];
	}
	
	public function scenarios(){
	
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['ip', 'desc', 'vlan', 'dhcp'];
		$scenarios[self::SCENARIO_UPDATE] = ['desc', 'dhcp'];
	
		return $scenarios;
	}
	
	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'id' => 'ID',
			'ip' => 'Podsieć',	
			'desc' => 'Opis',
			'vlan' => 'Vlan',
		);
	}
	
	public function getModelVlan(){
	
		//Connection ma tylko 1 Address
		return $this->hasOne(Vlan::className(), ['id' => 'vlan']);
	}
	
	public function getModelIps(){
	
		//Connection ma tylko 1 Address
		return $this->hasMany(Ip::className(), ['subnet' => 'id']);
	}
	
	private function getModelsDhcpValueForSubnet(){
	
// 		return DhcpValue::find()->select('option, value, weight')->where(['subnet' => $this->id])->orWhere(['dhcp_group' => $this->dhcp_group])->asArray()->all();
		return $this->hasMany(DhcpValue::className(), ['subnet' => 'id'])->select('option, value, weight')->asArray()->all();
	}
	
	private function getModelsDhcpValueForGroup(){
	
// 		return DhcpValue::find()->select('option, value, weight')->where(['subnet' => $this->id])->orWhere(['dhcp_group' => $this->dhcp_group])->asArray()->all();
		return $this->hasMany(DhcpValue::className(), ['dhcp_group' => 'dhcp_group'])->select('option, value, weight')->asArray()->all();
	}
	
	public function getBlockIp(){
		
		return new \IPv4Block($this->ip);
	}
	
	public function generateOptionsDhcp(){
		
		$options = [
			1 => ['option' => 1, 'value' => (string) $this->blockIp->getMask(), 'weight' => 1],
			3 => ['option' => 3, 'value' => (string) $this->blockIp[1], 'weight' => 1],
			6 => ['option' => 6, 'value' => '213.5.208.3, 213.5.208.35', 'weight' => 1],
			28 => ['option' => 28, 'value' => (string) $this->blockIp->getLastIp(), 'weight' => 1],
			49 => ['option' => 49, 'value' => 7200, 'weight' => 1]
		];
		
		
		foreach ($this->getModelsDhcpValueForGroup() as $group_option){
			
			if($group_option['option'] == $options[$group_option['option']]['option']){
                if($group_option['weight'] > $options[$group_option['option']]['weight'])
                	$options[$group_option['option']] = $group_option;
              }
             else
                $options[$group_option['option']] = $group_option;
		}

		foreach ($this->getModelsDhcpValueForSubnet() as $subnet_option){
				
			if($subnet_option['option'] == $options[$subnet_option['option']]['option']){
				if($subnet_option['weight'] > $options[$subnet_option['option']]['weight'])
					$options[$subnet_option['option']] = $subnet_option;
			}
			else
				$options[$subnet_option['option']] = $subnet_option;
		}
		
		return $options;
	}
}
