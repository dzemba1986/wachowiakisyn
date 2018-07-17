<?php

namespace backend\models;

use backend\models\configuration\ECSeriesConfiguration;
use backend\models\configuration\GSSeriesConfiguration;
use backend\models\configuration\XSeriesConfiguration;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @property boolean $dhcp
 * @property boolean $smtp
 * @property backend\models\Connection[] $connections
 */

class Host extends Device {
    
	const TYPE = 5;
	private $conf;
	
	public function init() {
		
	    $this->type_id = self::TYPE;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'dhcp',
	            'smtp'
	        ]
	    );
	}
	
	public function rules() {
		
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['mac', 'required', 'message' => 'Wartość wymagana', 'when' => function ($model) {return $model->status;}],
                ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
                
                ['dhcp', 'boolean'],
                ['dhcp', 'default', 'value' => true],
                ['dhcp', 'required', 'message' => 'Wartość wymagana'],
                
                ['smtp', 'boolean'],
                ['smtp', 'default', 'value' => false],
                ['smtp', 'required', 'message' => 'Wartość wymagana'],
            	
                [['mac', 'dhcp', 'smtp'], 'safe'],
            ]
        );       
	}
	
	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac', 'dhcp', 'smtp']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'dhcp', 'smtp']);
			
		return $scenarios;
	}
    
	public function attributeLabels() {
	    
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'dhcp' => 'DHCP',
                'smtp' => 'SMTP',
            ]
        ); 
	}
	
	public static function find() {
	    
	    return new DeviceQuery(get_called_class(), ['type_id' => self::TYPE]);
	}
	
	public function beforeSave($insert) {
	    
	    if (!$insert) {
	        if (array_key_exists('mac', $this->dirtyAttributes) && $this->oldAttributes['mac'] && $this->mac) {
	            
	            $history = \Yii::createObject([
	                'class' => History::className(),
	                'created_at' => date('Y-m-d H:i:s'),
	                'desc' => 'Zmiana adresu MAC z ' . Html::label($this->oldAttributes['mac']) . ' na ' . Html::label(strtolower($this->mac)),
	                'address_id' => $this->address_id,
	                'device_id' => $this->id,
	                'created_by' => \Yii::$app->user->id
	            ]);
	            
	            if (!$history->save()) throw new Exception('Błąd zapisu historii');
	        }
	        
	        if (array_key_exists('status', $this->dirtyAttributes) && $this->oldAttributes['status'] && !$this->status) {
	            
	            $history = \Yii::createObject([
	                'class' => History::className(),
	                'created_at' => date('Y-m-d H:i:s'),
	                'desc' => 'Dezaktywacja hosta',
	                'address_id' => $this->address_id,
	                'device_id' => $this->id,
	                'created_by' => \Yii::$app->user->id
	            ]);
	            
	            if (!$history->save()) throw new Exception('Błąd zapisu historii');
	        }
	        
	        if (array_key_exists('status', $this->dirtyAttributes) && !$this->oldAttributes['status'] && $this->status) {
	            
	            $history = \Yii::createObject([
	                'class' => History::className(),
	                'created_at' => date('Y-m-d H:i:s'),
	                'desc' => 'Aktywacja hosta z adresem MAC ' . Html::label(strtolower($this->mac)),
	                'address_id' => $this->address_id,
	                'device_id' => $this->id,
	                'created_by' => \Yii::$app->user->id
	            ]);
	            
	            if (!$history->save()) throw new Exception('Błąd zapisu historii');
	        }
	    }
	    
	    $this->type_id = self::TYPE;
	    return parent::beforeSave($insert);
	}
	
	
	function afterSave($insert, $changedAttributes) {
	    
	    if (!$insert) {
	        if (array_key_exists('mac', $changedAttributes) || array_key_exists('dhcp', $changedAttributes)) {
	            !empty($this->ips) ? Dhcp::generateFile($this->ips[0]->subnet) : null;
	        }
	    } else {
	        $history = \Yii::createObject([
	            'class' => History::className(),
	            'created_at' => date('Y-m-d H:i:s'),
	            'desc' => 'Aktywacja hosta z adresem MAC ' . Html::label(strtolower($this->mac)),
	            'address_id' => $this->address_id,
	            'device_id' => $this->id,
	            'created_by' => \Yii::$app->user->id
	        ]);
	        
	        if (!$history->save()) throw new Exception('Błąd zapisu historii');
	    }
	}
	
	public function getConnections() {

	    return $this->hasMany(Connection::className(), ['host_id' => 'id']);
	}
	
	function getConnectionsType() : array {
	    
	    if (empty($this->connections)) {
    	    foreach ($this->connections as $connection) {
                $types[] = $connection->type_id;
    	    }
	    } else $types = [];
	    
	    return $types;
	}
	
	public function configurationAdd() {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
    	    if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
    	    elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
    	    elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
    	    else return ' ';
	    } else return ' ';
	    
	    return $this->conf->add();
	}
	
	public function configurationDrop($auto = false) {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
    	    if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
    	    elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
    	    elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
    	    else return ' ';
	    } else return ' ';
	    
	    return $this->conf->drop($auto);
	}
	
	public function configurationChangeMac($newMac) {
	    
	    $parentId = $this->links[0]->parent_device;
	    $parentDevice = Device::findOne($parentId);
	    $parentModelConfType = $parentDevice->model->config;
	    
	    if (!empty($this->ips)) {
    	    if ($parentModelConfType == 1) $this->conf = new GSSeriesConfiguration($this, $parentDevice);
    	    elseif ($parentModelConfType == 2) $this->conf = new XSeriesConfiguration($this, $parentDevice);
    	    elseif ($parentModelConfType == 5) $this->conf = new ECSeriesConfiguration($this, $parentDevice);
    	    else return ' ';
        } else return ' ';
        
	    return $this->conf->changeMac($newMac);
	}
}