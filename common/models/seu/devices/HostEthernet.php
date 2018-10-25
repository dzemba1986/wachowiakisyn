<?php

namespace common\models\seu\devices;

use common\models\history\History;
use common\models\seu\devices\query\HostEthernetQuery;
use common\models\seu\devices\traits\Config;
use common\models\seu\devices\traits\Ip;
use common\models\seu\network\Dhcp;
use vakorovin\yii2_macaddress_validator\MacaddressValidator;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;

/**
 * @property string $mac
 * @property boolean $dhcp
 * @property boolean $smtp
 */

class HostEthernet extends Host {
    
    use Ip, Config;
    
	const TECHNIC = 1;
	
	public function init() {
		
	    $this->type_id = parent::TYPE;
	    $this->technic = self::TECHNIC;
		parent::init();
	}
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'mac',
	            'dhcp',
	            'smtp',
	        ]
        );
	}
	
	public function fields() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'mac',
	            'dhcp',
	            'smtp',
	        ]
        );
	}
	
	public function rules() {
		
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['mac', 'required', 'message' => 'Wartość wymagana'],
	            ['mac', 'string', 'min' => 12, 'max' => 17, 'tooShort' => 'Minimum {min} znaków', 'tooLong' => 'Maximum {max} znaków'],
	            ['mac', MacaddressValidator::className(), 'message' => 'Zły format'],
	            ['mac', 'filter', 'filter' => 'strtolower', 'skipOnEmpty' => TRUE],
	            ['mac', 'unique', 'targetClass' => self::className(), 'message' => 'Mac zajęty', 'when' => function ($model, $attribute) {
	                return strtolower($model->{$attribute}) !== strtolower($model->getOldAttribute($attribute));
	            }, 'filter' => ['status' => TRUE], 'on' => [self::SCENARIO_CREATE, self::SCENARIO_DEFAULT, self::SCENARIO_UPDATE]],
	            ['mac', 'trim', 'skipOnEmpty' => TRUE],
	            
	            ['dhcp', 'boolean'],
	            ['dhcp', 'default', 'value' => true],
	            ['dhcp', 'required', 'message' => 'Wartość wymagana'],
	            ['dhcp', 'filter', 'filter' => 'boolval'],
	            
	            ['smtp', 'boolean'],
	            ['smtp', 'default', 'value' => false],
	            ['smtp', 'required', 'message' => 'Wartość wymagana'],
	            ['smtp', 'filter', 'filter' => 'boolval'],
	            
	            [['mac', 'dhcp', 'smtp'], 'safe'],
	        ]
        );
	}
	
	public function scenarios() {
	    
		$scenarios = parent::scenarios();
		$scenarios[self::SCENARIO_CREATE] = ArrayHelper::merge($scenarios[self::SCENARIO_CREATE], ['mac', 'dhcp']);
		$scenarios[self::SCENARIO_UPDATE] = ArrayHelper::merge($scenarios[self::SCENARIO_UPDATE], ['mac', 'dhcp', 'smtp']);
			
		return $scenarios;
	}
    
	public function attributeLabels() {
	    
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'mac' => 'Mac',
                'dhcp' => 'DHCP',
                'smtp' => 'SMTP',
            ]
        ); 
	}
	
	public static function find() {
	    
	    return new HostEthernetQuery(get_called_class(), ['type_id' => parent::TYPE, 'technic' => self::TECHNIC]);
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
	    
	    $this->type_id = parent::TYPE;
	    $this->technic = self::TECHNIC;
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
}