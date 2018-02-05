<?php

namespace backend\models;

/**
 * @property integer $id
 * @property string $ip
 * @property string $desc
 * @property integer $vlan_id
 * @property boolean $dhcp
 * @property integer $dhcp_group
 */

class Subnet extends \yii\db\ActiveRecord
{
	const SCENARIO_CREATE = 'create';
	const SCENARIO_UPDATE = 'update';
	
	public static function tableName()
	{
		return '{{subnet}}';
	}
	
	public function rules()
	{
		return [
			['ip', 'required', 'message' => 'Wartość wymagana'],
			['ip', 'ip', 'subnet' => true, 'ipv6' => false, 'message' => 'Zły format adresu ip', 'wrongCidr' => 'Niewłasciwy prefix', 'noSubnet' => 'Brak prefiksu'],
				
			['desc', 'required', 'message' => 'Wartość wymagana'],
			['desc', 'string'],
				
			['vlan_id', 'required', 'message' => 'Wartość wymagana'],
			['vlan_id', 'integer', 'min' => 1, 'max' => 4096, 'tooSmall' => 'Wartość za mała', 'tooBig' => 'Wartość za duża', 'message' => 'Wartość liczbowa'],
			
		    ['dhcp', 'boolean'],
		    ['dhcp', 'requred', 'message' => 'Wartość wymagana'],
		    
			['dhcp_group', 'integer', 'message' => 'Wartość liczbowa'],

		    [['ip', 'desc', 'vlan_id', 'dhcp', 'dhcp_group'], 'safe'],
		];
	}
	
	public function scenarios(){
	
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ['ip', 'desc', 'vlan_id', 'dhcp'];
		$scenarios[self::SCENARIO_UPDATE] = ['desc', 'dhcp'];
	
		return $scenarios;
	}
	
	public function attributeLabels()
	{
		return [
			'ip' => 'Podsieć',	
			'desc' => 'Opis',
			'vlan_id' => 'Vlan',
		    'dhcp' => 'DHCP',
		    'dhcp_group' => 'Grupa DHCP'
		];
	}
	
	public function getVlan(){
	
		return $this->hasOne(Vlan::className(), ['id' => 'vlan_id']);
	}
	
	public function getIps(){
	
		return $this->hasMany(Ip::className(), ['subnet_id' => 'id']);
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
	
	public function getSize(){
		
		return $this->getBlockIp()->getNbAddresses() - 2;
	}
	
	public function getIPFreeCount(){
		
		return $this->getSize() - Ip::find()->where(['subnet' => $this->id])->count();
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
