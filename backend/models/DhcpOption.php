<?php

namespace backend\models;

class DhcpOption extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return '{{dhcp_option}}';
	}
	
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
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
				
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			[['code', 'subcode', 'rfc_name', 'name', 'type'], 'safe'],
		];
	}
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
    
	public function attributeLabels()
	{
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'rfc_name' => 'Nazwa RFC',
            ]
        ); 
	}
}
