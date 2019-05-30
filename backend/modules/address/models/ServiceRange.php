<?php

namespace backend\modules\address\models;

use common\models\soa\Coax;
use common\models\soa\Fiber;
use common\models\soa\Utp;
use common\models\soa\Utp3;
use yii\behaviors\AttributeBehavior;
use yii\db\ActiveRecord;

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
 * @property string dom
 * @property string dom_szczegol
 * @property string lokal_od
 * @property string lokal_do
 * @property integer utp
 * @property integer utp_cat3
 * @property integer coax
 * @property integer optical_fiber
 * @property integer net_utp
 * @property integer net_optical_fiber
 * @property integer netx_utp
 * @property integer netx_optical_fiber
 * @property integer phone_utp
 * @property integer phone_utp_cat3
 * @property integer hfc
 * @property integer iptv_utp
 * @property integer iptv_optical_fiber
 * @property integer rfog
 * @property integer iptv_net_utp
 * @property integer iptv_net_optical_fiber
 * @property integer iptv_netx_utp
 * @property integer iptv_netx_optical_fiber
 * @property integer rfog_net
 * @property integer rfog_netx
 */

class ServiceRange extends ActiveRecord {
    
    const SERVICE_INFRASTRUCTURE = [
        0, 3, 0, 3, 0, 1, 2, 0, 3, 3, 0, 3, 0, 3, 0, 3
    ];
    const INFRASTRUCTURE_ATTRIBUTES = [
        'utp', 'utp_cat3', 'coax', 'optical_fiber'
    ];
    
    const SERVICE_ATTRIBUTES = [
        'net_utp', 'net_optical_fiber', 'netx_utp', 'netx_optical_fiber', 'phone_utp', 'phone_utp_cat3', 'hfc', 'iptv_utp', 'iptv_optical_fiber', 'rfog',
        'iptv_net_utp', 'iptv_net_optical_fiber', 'iptv_netx_utp', 'iptv_netx_optical_fiber', 'rfog_net', 'rfog_netx'
    ];
    private $_teryt = null;
    
	public static function tableName() : string {
		
		return '{{range}}';
	}
	
	public function rules() : array {
		
		return [
		    ['t_ulica', 'required', 'message' => 'Wartość wymagana'],
			
		    ['dom', 'string', 'min' => 1, 'max' => 10],
		    ['dom', 'required', 'message' => 'Wartość wymagana'],
		    ['dom', 'trim'],
		    
		    ['dom_szczegol', 'string', 'min' => 1, 'max' => 50],
		    ['dom_szczegol', 'default', 'value' => null],
		    ['dom_szczegol', 'filter', 'filter' => 'strtoupper'],
		    ['dom_szczegol', 'trim'],
		    
		    ['lokal_od', 'string', 'min' => 1, 'max' => 10],
		    ['lokal_od', 'default', 'value' => null],
		    ['lokal_od', 'trim'],

		    ['lokal_do', 'string', 'min' => 1, 'max' => 10],
		    ['lokal_do', 'default', 'value' => null],
		    ['lokal_do', 'trim'],

		    [self::INFRASTRUCTURE_ATTRIBUTES, 'number', 'integerOnly' => true, 'min' => -1, 'max' => 3],
		    [self::INFRASTRUCTURE_ATTRIBUTES, 'required', 'message' => 'Wartość wymagana'],

		    [self::SERVICE_ATTRIBUTES, 'number', 'integerOnly' => true, 'min' => 0, 'max' => 2],
		    [self::SERVICE_ATTRIBUTES, 'required', 'message' => 'Wartość wymagana'],
		    
			[['t_woj', 't_pow', 't_gmi', 't_rodz', 't_miasto', 't_ulica', 'ulica_prefix', 'ulica', 'dom', 'dom_szczegol', 'lokal_od', 'lokal_do', 'rfog', 'iptv', 'internet', 'dvb_c', 'phone'], 'safe'],
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
			'ulica' => 'Ulica',	
			'dom' => 'Dom',
		    'dom_szczegol' => 'Klatka',
		    'lokal_od' => 'Lokal od',
		    'lokal_do' => 'Lokal do',
		    'utp' => 'UTP',
		    'utp_cat3' => 'UTP3',
		    'coax' => 'COAX',
		    'optical_fiber' => 'FIBER',
		    'net_utp' => 'NET_UTP',
		    'net_optical_fiber' => 'NET_OF',
		    'netx_utp' => 'NETX_UTP',
		    'netx_optical_fiber' => 'NETX_OF',
		    'phone_utp' => 'PHONE_UTP',
		    'phone_utp_cat3' => 'PHONE_UTP3',
		    'hfc' => 'HFC',
		    'iptv_utp' => 'IPTV_UTP',
		    'iptv_optical_fiber' => 'IPTV_OF',
		    'rfog' => 'RFOG',
		    'iptv_net_utp' => 'IPTV_NET_UTP',
		    'iptv_net_optical_fiber' => 'IPTV_NET_OF',
		    'iptv_netx_utp' => 'IPTV_NETX_UTP',
		    'iptv_netx_optical_fiber' => 'IPTV_NETX_OF',
		    'rfog_net' => 'RFOG_NET',
		    'rfog_netx' => 'RFOG_NETX',
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
	    
	    if (is_null($this->_teryt)) $this->_teryt = $this->hasOne(Teryt::class, ['t_ulica' => 't_ulica'])->asArray()->one();
	    
	    return $this->_teryt;
	}
	
	public function getInstallsCount() {
	    
	    $infras = [Utp::TYPE => $this->utp, Utp3::TYPE => $this->utp_cat3, Coax::TYPE => $this->coax, Fiber::TYPE => $this->optical_fiber];
	    $out = [];
	    foreach ($infras as $key => $infra) {
	        if ($infra >= 0) $out[$key] = $infra;
	        elseif ($infra == -1) {
	            $out[$key] = $this->address->getInstallations()->select('type_id')->where(['type_id' => $key])->asArray()->all()['type_id'] ? 1 : 0;
	        }
	    }
	    
	    return $out;
	}
	
	public function checkServices() {
	    
	    return $this->getInfra();
	}
}
