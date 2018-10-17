<?php

namespace common\models\seu\network;

use yii\db\ActiveRecord;

class DhcpOption extends ActiveRecord
{
	public static function tableName() {
		return '{{dhcp_option}}';
	}
	
	public function rules() {
		return [
			['code', 'required', 'message' => 'Wartość wymagana'],
			['code', 'integer', 'message' => 'Wartość musi być liczbą'],
				
			['subcode', 'required', 'message' => 'Wartość wymagana'],
			['subcode', 'integer', 'message' => 'Wartość musi być liczbą'],
			
			['rfc_name', 'required', 'message' => 'Wartość wymagana'],
			['rfc_name', 'string'],
			
			['name', 'required', 'message' => 'Wartość wymagana'],
			['name', 'string'],
				
			['type', 'required', 'message' => 'Wartość wymagana'],
			['type', 'string'],
				
			[['code', 'subcode', 'rfc_name', 'name', 'type'], 'safe'],
		];
	}
    
	public function attributeLabels() {
        return [
            'rfc_name' => 'Nazwa RFC',
        ];
	}
}
