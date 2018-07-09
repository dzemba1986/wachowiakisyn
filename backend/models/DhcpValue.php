<?php

namespace backend\models;

use yii\db\ActiveRecord;

class DhcpValue extends ActiveRecord {
    
	public static function tableName() {
	    
		return '{{dhcp_value}}';
	}
	
	public function rules() {
	    
		return [
			['weight', 'required', 'message' => 'Wartość wymagana'],
			['weight', 'integer', 'message' => 'Wartość musi być liczbą'],
				
			['option', 'required', 'message' => 'Wartość wymagana'],
			['option', 'integer', 'message' => 'Wartość musi być liczbą'],
			
			['value', 'required', 'message' => 'Wartość wymagana'],
			['value', 'string'],
			
			['subnet', 'required', 'message' => 'Wartość wymagana'],
			['subnet', 'integer'],
				
			['dhcp_group', 'required', 'message' => 'Wartość wymagana'],
			['dhcp_group', 'integer'],
				
			[['weight', 'option', 'value', 'subnet', 'dhcp_group'], 'safe'],
		];
	}
}
