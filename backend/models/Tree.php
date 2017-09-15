<?php

namespace backend\models;

use Yii;
use backend\models\Device;

/**
 * This is the model class for table "tbl_agregation".
 *
 * @property intiger $device
 * @property string $port
 * @property string $parent_device
 * @property string $parent_port
 */
class Tree extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{agregation}}';
    }

    public function rules()
    {
        return [	
        		
        	['device', 'required', 'message'=>'Wartość wymagana'],
        		
        	['port', 'required', 'message'=>'Wartość wymagana'],
        		
        	['parent_device', 'required', 'message'=>'Wartość wymagana'],
        		
        	['parent_port', 'required', 'message'=>'Wartość wymagana'],
//         		['device', 'port', 'parent_device', 'parent_port'],
//         	 	'integer'
//         	],
//            [//artybuty wymagane
//            	['title', 'start', 'end', 'address', 'add_user', 'add'], 
//            	'required',	'message'=>'Wartość jest wymagana',
//            ],
            [
            	['device', 'port', 'parent_device', 'parent_port'], 
				'safe',
			],
        ];
    }
    
    public function getModelDevice(){
    
    	// agregacja dotyczy 1 urządzenia
    	return $this->hasOne(Device::className(), ['id'=>'device']);
    }
    
    public function getParents($id) {
        
        //SELECT parent_device FROM Agregacja WHERE device='$dev_id' AND uplink='1' LIMIT 1
        
    }
	
    public static function getIdDevice($id){
    	
    	return (int) substr($id, 0, stripos($id, '.'));
    }
    
    public static function getPortDevice($id){
    	
    	return (int) substr($id, stripos($id, '.') + 1, stripos($id, '-') - stripos($id, '.') + 1);
    }

    public function attributeLabels()
    {
        return [
            'device' => 'Urządzenie',
            'port' => 'Port',
            'parent_device' => 'Urzadzenie nadrzędne',
            'parent_port' => 'Port nadrzędny',
        ];
    }
}


