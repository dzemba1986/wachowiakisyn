<?php

namespace common\models\crm;

use common\models\crm\query\TaskQuery;

class ServiceTask extends Task {
    
    const TYPE = 1;
    
    public static function getDb() {
        
        return \Yii::$app->wtvkDb;
    }
    
    public static function tableName() {
        
        return '{{zgloszenie_serwis}}';
    }
    
    public function attributes() {
    	
    	return [
			'kategoria', 
			'rodzaj', 
			'data_zgloszenia', 
			'czas_zgloszenia',
			'opis',
			'data_interwencji',
		    'czas_interwencji',
		    'status',
		    'data_fakt_interwencji',
		    'czas_fakt_interwencji',
		    'opis_interwencji',
		    'abonent',
		    'utworzyl',
		    'odpowiedzialny',
		    'zamknal'
		];
    }
    
    public static function find() {
        
        return new TaskQuery(get_called_class(), ['type_id' => self::TYPE, 'columns' => self::columns()]);
    }
}