<?php

namespace common\models\seu;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property integer $port_count
 * @property integer $type
 * @property integer $manufacturer
 * @property array $port
 */

class Model extends ActiveRecord {
    
	public static function tableName() {
	    
		return '{{model}}';
	}
	
	public function rules() {
	    
		return [
		    ['name', 'string'],
		    ['name', 'required', 'message' => 'Wartość wymagana'],
		    
		    ['port_count', 'integer'],
		    ['port_count', 'required', 'message' => 'Wartość wymagana'],
		    
		    ['port', 'required', 'message' => 'Wartość wymagana'],
		    //['port', 'each', 'rule' => ['string']],
		    
		    ['type_id', 'required', 'message' => 'Wartość wymagana'],
		    
		    ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
		    
			[['name', 'port_count', 'type_id', 'manufacturer_id', 'port'], 'safe'],
		];
	}
	
	public function attributeLabels() {
	    
		return [
			'name' => 'Nazwa',
		];
	}
}