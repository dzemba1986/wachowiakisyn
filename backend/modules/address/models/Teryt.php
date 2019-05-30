<?php

namespace backend\modules\address\models;

use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\db\Query;

/**
 * @property integer $id
 * @property string t_woj
 * @property string t_pow
 * @property string t_gmi
 * @property string t_rodz
 * @property string t_miasto
 * @property string t_ulica
 * @property string ulica_prefix
 * @property string ulica
 * @property string $name
 * @property integer $config
 */

class Teryt extends ActiveRecord {
    
    private $_teryt;
	public static function tableName() : string {
		
		return '{{teryt}}';
	}
	
	public function rules() : array {
		
		return [
			['t_gmi', 'string', 'min' => 1, 'max' => 2],
			['t_gmi', 'trim'],
			
			['t_rodz', 'string', 'min' => 1, 'max' => 1],
			['t_rodz', 'default', 'value' => '9'],
			['t_rodz', 'trim'],
			
			['t_miasto', 'string', 'min' => 7, 'max' => 7],
			['t_miasto', 'trim'],
			
			['t_ulica', 'string', 'min' => 5, 'max' => 7],
			[['t_ulica', 't_miasto'], 'unique', 'targetAttribute' => ['t_ulica', 't_miasto'], 'message' => 'Ulica istnieje'], 
			
			['ulica_prefix', 'string', 'min' => 1, 'max' => 3],
			['ulica_prefix', 'trim'],
			
			['ulica', 'string', 'min' => 2, 'max' => 255],
			['ulica', 'required', 'message' => 'Wartość wymagana'],
			['ulica', 'trim'],
			
			['name', 'required', 'message' => 'Wartość Wymagana'],
			['name', 'string', 'min' => 2, 'max' => 5, 'tooShort' => 'Min {min} znaki', 'tooLong' => 'Max {max} znaków'],
			['name', 'match', 'pattern' => '/^[a-zA-Z]{1,5}$/', 'message' => 'Tylko litery'],
			['name', 'filter', 'filter' => 'strtoupper'],
			
			[['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 'ulica_prefix', 'ulica', 'name'], 'safe'],
		];
	}
	
	public function attributeLabels() : array {
		
		return [
			'id' => 'ID',
			't_miasto' => 'Teryt miasto',
			't_woj' => 'Teryt województwo',
			't_pow' => 'Teryt powiat',
			't_gmi' => 'Teryt gmina',
			't_rodz' => 'Teryt rodzaj',
			't_ulica' => 'Teryt ulica',	
			'ulica_prefix' => 'prefix',	
			'name' => 'Skrót',
			'ulica' => 'Ulica',	
			'config' => 'Konfiguracja'
		];
	}
	
	public function behaviors() {
	    
	    return [
	        [
	            'class' => AttributeBehavior::class,
	            'attributes' => [
	                self::EVENT_BEFORE_INSERT => 't_woj',
	            ],
	            'value' => '30',
	        ],
	        [
	            'class' => AttributeBehavior::class,
	            'attributes' => [
	                self::EVENT_BEFORE_INSERT => 't_pow',
	            ],
	            'value' => '64',
	        ],
	        [
	            'class' => AttributeBehavior::class,
	            'attributes' => [
	                self::EVENT_BEFORE_INSERT => 't_gmi',
                ],
                'value' => function () { return $this->getTeryt()['t_gmi']; },
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 't_rodz',
                ],
            'value' => function () { return $this->getTeryt()['t_rodz']; },
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 't_miasto',
                ],
            'value' => function () { return $this->getTeryt()['t_miasto']; },
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'ulica_prefix',
                ],
            'value' => function () { return $this->getTeryt()['ulica_prefix']; },
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => 'ulica',
                ],
            'value' => function () { return $this->getTeryt()['ulica']; },
            ],
        ];
	}
	
	private function getTeryt() {
	    
	    if (is_null($this->_teryt)) {
	        $this->_teryt = (new Query())
	        ->select(['t_gmi' => 'gmi', 't_rodz' => 'rodz_gmi', 't_miasto' => 'sym', 't_ulica' => 'sym_ul', 'ulica_prefix' => 'cecha', 
	            'ulica' => new Expression("CONCAT(nazwa_2, ' ', nazwa_1)")
	        ])->from('ulic')->where(['and', ['woj' => '30'], ['pow' => '64'], ['rodz_gmi' => '9'], ['sym_ul' => $this->t_ulica]])->one();
	    }
	    
	    return $this->_teryt;
	}
	
	public static function findOrderStreetName() {
		
		return self::find()->select(['t_ulica', 'ulica'])->orderBy('ulica')->all();
	}
}
