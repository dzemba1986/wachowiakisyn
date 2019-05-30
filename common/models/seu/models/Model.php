<?php

namespace common\models\seu;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $name
 * @property integer $type_id
 * @property integer $manufacturer_id
 * @property array $ports
 */

abstract class Model extends ActiveRecord {
    
	public static function tableName() {
	    
		return '{{model}}';
	}
	
	public function rules() {
	    
		return [
		    ['name', 'string'],
		    ['name', 'required', 'message' => 'Wartość wymagana'],
		    
		    ['type_id', 'required', 'message' => 'Wartość wymagana'],
		    
		    ['manufacturer_id', 'required', 'message' => 'Wartość wymagana'],
		    
			[['name', 'type_id', 'manufacturer_id'], 'safe'],
		];
	}
	
	public function attributeLabels() {
	    
		return [
			'name' => 'Nazwa',
		    'type_id' => 'Typ urządzenia',
		    'manufacturer_id' => 'Producent',
		];
	}
	
	abstract function getPorts() : array;
}