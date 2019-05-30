<?php

namespace common\models\soa;

use yii\db\ActiveRecord;
/**
 * @property integer $id
 * @property string $name
 * @property intiger $type_id
 * @property string $download
 * @property string $upload
 */

class Package extends ActiveRecord {
    
	public static function tableName() {
	    
		return '{{connection_package}}';
	}

	public function rules() {
	    
		return [
		    ['name', 'string'],
		    ['name', 'requred'],
		    
		    ['parent_id', 'integer'],
		    ['parent_id', 'requred'],
		    
			[['name', 'parent_id'], 'safe'],
		];
	}
	
	public function attributeLabels() {
	    
		return [
			'name' => 'Nazwa',
		];
	}
}
