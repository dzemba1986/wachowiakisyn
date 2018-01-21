<?php

namespace app\models;

use yii\db\ActiveRecord;
/**
 * @property integer $id
 * @property string $name
 * @property intiger $type_id
 * @property string $download
 * @property string $upload
 */

class Package extends ActiveRecord
{
	public static function tableName()
	{
		return '{{package}}';
	}

	public function rules()
	{
		return [
		    ['name', 'string'],
		    ['name', 'requred'],
		    
		    ['type_id', 'integer'],
		    ['type_id', 'requred'],
		    
		    ['download', 'integer'],
		    
		    ['upload', 'integer'],
		    
			[['name', 'type', 'download', 'upload'], 'safe'],
		];
	}
	
	public function attributeLabels()
	{
		return array(
			'name' => 'Nazwa',
			'type_id' => 'Typ',
			'download' => 'Download',
			'upload' => 'Upload',
		);
	}
}
