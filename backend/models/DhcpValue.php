<?php

namespace backend\models;

class DhcpValue extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return '{{dhcp_value}}';
	}
	
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
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
				
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			[['weight', 'option', 'value', 'subnet', 'dhcp_group'], 'safe'],
		];
	}
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
    
	public function attributeLabels()
	{
        return [
        	//'rfc_name' => 'Nazwa RFC',
        ];
	}
}
