<?php

namespace backend\models;

class DhcpGroup extends \yii\db\ActiveRecord
{
	public static function tableName()
	{
		return 'dhcp_group';
	}
	
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return [
			['name', 'required', 'message' => 'Wartość wymagana'],
			['name', 'string'],
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			[['name'], 'safe'],
		];
	}
    
	/**
	 * @return array customized attribute labels (name=>label)
	 */
    
	public function attributeLabels()
	{
        return [
        	'name' => 'Nazwa',
        ];
	}
}
