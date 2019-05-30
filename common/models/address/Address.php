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
 * @property InstallTask[] $tasks
 * @property Connection[] $connections
 */

class Address extends ActiveRecord {
    
	const SCENARIO_UPDATE = 'update';
	
	private $_teryt = null;
    private $_range = null;
    
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
	
	public function getInstallations() {
	
		return $this->hasMany(Installation::class, ['address_id' => 'id']);
	}
	
	public function getConnections() {
	
		return $this->hasMany(Connection::class, ['address_id' => 'id']);
	}

	public function getActiveConnections() {
	
		return $this->hasMany(Connection::class, ['address_id' => 'id'])->where(['close_at' => null]);
	}
	
	public function getDevices() {
	
		return $this->hasMany(Device::class, ['address_id' => 'id']);
	}
	
	public function getTasks() {
	
		return $this->hasMany(Task::class, ['address_id' => 'id']);
	}
	
	public function getHistories() {
	    
	    return $this->hasMany(History::class, ['address_id' => 'id']);
	}
	
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

	public function getServiceRange() {
	    
	    if (is_null($this->_range)) {
            $services = ServiceRange::find()->where(['t_ulica' => $this->t_ulica, 'dom' => $this->dom])->all();
            $servicesFilters1 = $servicesFilters2 = $servicesFilters3 = [];
	        //filtracja po klatce
	        if ($this->dom_szczegol) {
	            foreach ($services as $service) {
	                if ($this->dom_szczegol == $service->dom_szczegol) array_push($servicesFilters1, $service);
	            }
	        } else {
	            foreach ($services as $service) {
	                if (!$service->dom_szczegol) array_push($servicesFilters1, $service);
	            }
	        }
	        //filtracja po lokalu
	        if ($this->lokal) {
	            foreach ($servicesFilters1 as $service) {
	                if (!$service->lokal_od && !$service->lokal_do) array_push($servicesFilters3, $service);
	                if ($service->lokal_od && $service->lokal_do && ($this->lokal >= $service->lokal_od) && ($this->lokal <= $service->lokal_do)) 
	                    array_push($servicesFilters2, $service);
	            }
	            if (!$servicesFilters2) $servicesFilters2 = $servicesFilters3;
	        }
    	    $this->_range = count($servicesFilters2) == 1 ? $servicesFilters2[0] : false;
	    }
        
	    return $this->_range;
	}
    
	/**
	 * Check address if exist.
	 *
	 * @return integer|bool ID founded address or false if not exist.
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
                return $this->ulica_prefix . ' '.$this->ulica . ' ' . $this->dom . $this->dom_szczegol . '/'.$this->lokal . $this->lokal_szczegol . ' (piętro ' . $this->pietro . ')';
            else
                return $this->ulica_prefix . ' ' . $this->ulica . ' ' . $this->dom . $this->dom_szczegol . ' (piętro ' . $this->pietro . ')';
	}
}
