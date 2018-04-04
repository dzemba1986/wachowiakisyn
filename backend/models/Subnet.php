<?php

namespace backend\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * @property integer $id
 * @property string $ip
 * @property string $desc
 * @property integer $vlan_id
 * @property boolean $dhcp
 * @property integer $dhcp_group
 * @property \IPv4Block $blockIp
 */

class Subnet extends ActiveRecord
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
		    ['dhcp', 'required', 'message' => 'Wartość wymagana'],
		    
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
	
	private function getDhcpValueForSubnet(){
	
		return $this->hasMany(DhcpValue::className(), ['subnet_id' => 'id'])->select('option, value, weight')->asArray()->all();
	}
	
	private function getDhcpValueForGroup(){
	
		return $this->hasMany(DhcpValue::className(), ['dhcp_group' => 'dhcp_group'])->select('option, value, weight')->asArray()->all();
	}
	
	public function getBlockIp(){
		
		return new \IPv4Block($this->ip);
	}
	
	public function getSize(){
		
		return $this->getBlockIp()->getNbAddresses() - 2;
	}
	
	public function getIPFreeCount(){
		
		return $this->getSize() - Ip::find()->where(['subnet_id' => $this->id])->count();
	}
	
	/**
	 * Metoda generuje opcje DHCP dla wybranej podsieci.
	 * Na początku są ustawiane domyślne opcje w tablicy $options[].
	 * Jeżeli chcemy przysłonić te ocje to w tabeli `dhcp_value` ustawiamy odpowiednią opcję dla grupy lub dla pojedyńczej podsieci
	 * z wagą > 1 (wagę 1 mają domyslne ocje) według reguły:
	 * waga 3 dla opcji dla pojedyńczej podsieci
	 * waga 2 dla opcji dla grupy podsieci
	 */
	public function generateOptionsDhcp() {
		
		$options = [
			1 => ['option' => 1, 'value' => (string) $this->blockIp->getMask(), 'weight' => 1],
			3 => ['option' => 3, 'value' => (string) $this->blockIp[1], 'weight' => 1],
			6 => ['option' => 6, 'value' => '213.5.208.3, 213.5.208.35', 'weight' => 1],
			28 => ['option' => 28, 'value' => (string) $this->blockIp->getLastIp(), 'weight' => 1],
			//49 => ['option' => 49, 'value' => 7200, 'weight' => 1]
		];
		
		//jeżeli znajdzie opcje z wyższą wagą wśród opcji dla grupy DHCP to podmienia
		foreach ($this->getDhcpValueForGroup() as $groupOption) {
		    if (array_key_exists($groupOption['option'], $options)) {
		        if($groupOption['weight'] > $options[$groupOption['option']]['weight'])
		            $options[$groupOption['option']] = $groupOption; 
		    } else 
		        $options[$groupOption['option']] = $groupOption;
		}
        
		//jeżeli znajdzie opcje z wyższą wagą wśród opcji dla podsieci to podmienia
		foreach ($this->getDhcpValueForSubnet() as $subnetOption) {
		    if(array_key_exists($subnetOption['option'], $options)) {
				if($subnetOption['weight'] > $options[$subnetOption['option']]['weight'])
					$options[$subnetOption['option']] = $subnetOption;
			} else 
			    $options[$subnetOption['option']] = $subnetOption;
		}
		
		//TODO sprawdzić w DB bo nie wszystkie opcje mają nazwy
		$dhcpOptions = ArrayHelper::map(DhcpOption::find(array_keys($options))->select(['id', 'name'])->all(), 'id', 'name');
		
		$data = '';
		
		foreach ($options as $option) {
		    $data .= "\t{$dhcpOptions[$option['option']]} {$option['value']};\n";
		}
		
		//TODO tymczasowo tak, potem do poprawki by można było pojedyńczą podsieć skonfigurować indywidualnie
		$data .= "\tdefault-lease-time 86400;\n";
		$data .= "\tmax-lease-time 86400;\n";
		$data .= "\tmin-lease-time 86400;\n";
		//$data .= "\tdefault-lease-time" . $option['value'] . ";\n";
		$data .= "\n";
		
		return $data;
	}
}
