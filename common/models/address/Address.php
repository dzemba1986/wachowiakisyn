<?php

namespace common\models\address;

use backend\modules\address\models\ServiceRange;
use backend\modules\address\models\Teryt;
use common\models\crm\Task;
use common\models\history\History;
use common\models\history\HistoryIp;
use common\models\seu\devices\Device;
use common\models\soa\Connection;
use common\models\soa\Installation;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

/**
 * This is the model class for table 'address'.
 *
 * The followings are the available columns in table 'address':
 * @property integer $id PK
 * @property string $t_woj
 * @property string $t_pow
 * @property string $t_gmi
 * @property string $t_rodz
 * @property string $t_miasto
 * @property string $t_ulica
 * @property string $ulica_prefix
 * @property string $ulica
 * @property string $dom
 * @property string $dom_szczegol
 * @property string $lokal
 * @property string $lokal_szczegol
 * @property string $pietro
 * @property Installation[] $installations
 * @property Device[] $devices
 * @property Task[] $tasks
 * @property Connection[] $connections
 */

class Address extends ActiveRecord {
    
	const SCENARIO_UPDATE = 'update';
	
	private $_teryt = null;
    private $_serviceRange = null;
    
	public static function tableName() : string {
		
		return '{{address}}';
	}
	
	public function rules() : array {

	    return [
			['t_ulica', 'required', 'message' => 'Wartość wymagana'],
				
			['dom', 'string', 'min' => 1, 'max' => 10],
			['dom', 'required', 'message' => 'Wartość wymagana'],
			['dom', 'trim'],				
				
			['dom_szczegol', 'string', 'min' => 1, 'max' => 50],
			['dom_szczegol', 'default', 'value' => ''],
			['dom_szczegol', 'filter', 'filter' => 'strtoupper'],
			['dom_szczegol', 'trim'],
			
			['lokal', 'string', 'min' => 1, 'max' => 10],
			['lokal', 'default', 'value' => ''],
			['lokal', 'trim'],
			
			['lokal_szczegol', 'string', 'min' => 1, 'max' => 50],
			['lokal_szczegol', 'default', 'value' => ''],
			['lokal_szczegol', 'filter', 'filter'=>'strtoupper'],
			['lokal_szczegol', 'trim'],
			
			['pietro', 'string', 'min' => -1, 'max' => 2],
			['pietro', 'default', 'value' => ''],
				
			[['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'], 'safe'],
		];
	}
	
	public function scenarios() : array {
	
		$scenarios = parent::scenarios();
		//TODO jeżeli WTVK wyjdzie poza Poznań, trzeba to uaktualnić
		$scenarios[self::SCENARIO_UPDATE] = ['t_miasto', 't_ulica', 't_gmi', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal', 'lokal_szczegol', 'pietro'];
	
		return $scenarios;
	}
	
	public function attributeLabels() : array {
		
		return [
			'id' => 'ID',
			'ulica_prefix' => 'Prefix',
			'dom' => 'Dom',
			'dom_szczegol' => 'Dom szczegół',
			'lokal' => 'Lokal',
			'lokal_szczegol' => 'Lokal szczegół',
			'pietro' => 'Piętro'
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
	
	public function save($runValidation = false, $attributeNames = null) {
		
	    $out = false;
	    if ($this->getIsNewRecord()) {
	        if ($this->validate()) {
                $exist = $this->checkExist();
                if ($exist) {
                    $this->setIsNewRecord(false);
                    $this->id = $exist;
                } else $out = $this->insert($runValidation, $attributeNames);
	        }
	    } else $out = $this->update($runValidation, $attributeNames) !== false;
        
	    return $out;
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getInstallations() {
	
		return $this->hasMany(Installation::class, ['address_id' => 'id']);
	}
    
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getConnections() {
	
		return $this->hasMany(Connection::class, ['address_id' => 'id']);
	}
    
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getDevices() {
	
		return $this->hasMany(Device::class, ['address_id' => 'id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getTasks() {
	
		return $this->hasMany(Task::class, ['address_id' => 'id']);
	}
	
	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getHistories() {
	    
	    return $this->hasMany(History::class, ['address_id' => 'id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getHistoryIps() {
	    
	    return $this->hasMany(HistoryIp::class, ['address_id' => 'id']);
	}
	
	public static function getFloor() : array {
	    
	    return range(-2, 16);
	}
	
	private function getTeryt() {
	    
	    if (is_null($this->_teryt)) $this->_teryt = $this->hasOne(Teryt::class, ['t_ulica' => 't_ulica'])->asArray()->one();
        
        return $this->_teryt;
	}

	/**
	 * Zwraca zakres z dostępnymi usługami który jest przypisany danemu adresowi. W przypadku jego nie odnalezienia zwraca fałsz.
	 * 
	 * @return boolean|\backend\modules\address\models\ServiceRange Przypisany do adresu zakres z umowami lub fałsz w innym przypadku
	 */
	public function getServiceRange() {
	    
        $this->_serviceRange = false;
	    $address = [
	        't_ulica' => $this->t_ulica, 
	        'dom' => $this->dom, 
	        'dom_szczegol' => $this->dom_szczegol, 
	        'lokal' => $this->lokal, 
	        'lokal_szczegol' => $this->lokal_szczegol
	    ];
        
        $ranges = ServiceRange::find()->select([
            'id', 't_ulica', 'dom', 'dom_szczegol', 'lokal_od', 'lokal_do'
        ])->where(['t_ulica' => $this->t_ulica])->asArray()->all();
        $rangesF1 = $rangesF2 = $rangesF3 = $rangesF4 = [];
        
        foreach ($ranges as $range) {
            if ($range['dom'] == $address['dom'] && $range['dom_szczegol'] == $address['dom_szczegol'] && $range['lokal_od'] && $range['lokal_do'] &&
                $address['lokal'] >= $range['lokal_od'] && $address['lokal'] <= $range['lokal_do']) array_push($rangesF1, $range);

            if ($range['dom'] == $address['dom'] && $range['dom_szczegol'] == $address['dom_szczegol'] && is_null($range['lokal_od']) && is_null($range['lokal_do']))
                array_push($rangesF2, $range);

            if ($range['dom'] == $address['dom'] && is_null($range['dom_szczegol']) && is_null($range['lokal_od']) && is_null($range['lokal_do']))
                array_push($rangesF3, $range);

            if (is_null($range['dom']) && is_null($range['dom_szczegol']) && is_null($range['lokal_od']) && is_null($range['lokal_do']))
                array_push($rangesF4, $range);
        }
        
        $filters = [$rangesF1, $rangesF2, $rangesF3, $rangesF4];
        
        foreach ($filters as $filter) {
            if (!$filter) continue;
            elseif (count($filter) == 1) {
                $this->_serviceRange = ServiceRange::findOne($filter[0]['id']);
                $this->_serviceRange->address_ulica = $this->ulica;
                $this->_serviceRange->address_dom = $this->dom;
                $this->_serviceRange->address_dom_szczegol = $this->dom_szczegol;
                $this->_serviceRange->address_lokal = $this->lokal;
                $this->_serviceRange->address_lokal_szczegol = $this->lokal_szczegol;
                break;
            }
        }
        
        return $this->_serviceRange;
	}
    
	/**
	 * Sprawdza czy istnieje już taki sam adres.
	 *
	 * @return integer|bool ID znalezionego adresu lub false w innym przypadku.
	 */
	private function checkExist() {
	
		$exist = false;
	    $exist = self::find()->select('id')->where([
		    't_ulica' => $this->t_ulica, 
			'dom' => $this->dom, 
			'dom_szczegol' => $this->dom_szczegol, 
			'lokal' => $this->lokal,
			'pietro' => $this->pietro
		])->asArray()->one()['id'];
		
		return $exist; 
	}
	
	public function getShort() {
	    
        if (!$this->pietro)
            if ($this->lokal)
                return $this->getTeryt()['name'] . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol;
            else
                return $this->getTeryt()['name'] . $this->dom . $this->dom_szczegol;
        else
            if ($this->lokal)
                return $this->getTeryt()['name'] . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol . 'p' . $this->pietro;
            else
                return $this->getTeryt()['name'] . $this->dom . $this->dom_szczegol . 'p' . $this->pietro;
	                                
	}
	
	public function __toString() {
	    
	    if (!$this->pietro)
	        if ($this->lokal)
	            return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol;
            else
                return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol;
        else
            if ($this->lokal)
                return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol . '/' . $this->lokal . $this->lokal_szczegol . ' (piętro ' . $this->pietro . ')';
            else
                return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol . ' (piętro ' . $this->pietro . ')';
	}
}
