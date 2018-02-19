<?php

namespace backend\models;

use yii\db\ActiveRecord;

class DhcpGroup extends ActiveRecord
{
	public static function tableName()
	{
		return '{{dhcp_group}}';
	}
	
	public function rules()
	{
		return [
			['name', 'required', 'message' => 'Wartość wymagana'],
			['name', 'string'],
			
		    [['name'], 'safe'],
		];
	}
    
	public function attributeLabels()
	{
        return [
        	'name' => 'Nazwa',
        ];
	}
}
