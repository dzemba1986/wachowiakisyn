<?php

namespace common\models\crm;

use yii\db\ActiveRecord;
/**
 * @property integer $id
 * @property string $name
 * @property intiger $type_id
 * @property string $download
 * @property string $upload
 */

class TaskCategory extends ActiveRecord {
    
	public static function tableName() {
	    
		return '{{task_category}}';
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
