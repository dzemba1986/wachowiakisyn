<?php

namespace backend\modules\address\models;

use common\models\soa\Coax;
use common\models\soa\Fiber;
use common\models\soa\Installation;
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
 * @property integer net_1g_utp
 * @property integer net_1g_opt
 * @property integer net_10g_utp
 * @property integer net_10g_opt
 * @property integer phone
 * @property integer hfc
 * @property integer iptv_utp
 * @property integer iptv_opt
 * @property integer rfog
 * @property integer iptv_net_1g_utp
 * @property integer iptv_net_1g_opt
 * @property integer iptv_net_10g_utp
 * @property integer iptv_net_10g_opt
 * @property integer rfog_net_1g
 * @property integer rfog_net_10g
 */

class ServiceRange extends ActiveRecord {
    
    const INFRASTRUCTURES = [
        'utp', 'utp_cat3', 'coax', 'optical_fiber'
    ];
    
    const SERVICES = [
        'net_1g_utp', 'net_1g_opt', 'net_10g_utp', 'net_10g_opt', 'phone', 'hfc', 'iptv_utp', 'iptv_opt', 'rfog',
        'iptv_net_1g_utp', 'iptv_net_1g_opt', 'iptv_net_10g_utp', 'iptv_net_10g_opt', 'rfog_net_1g', 'rfog_net_10g'
    ];
    
//     const SERVICES_INFO = [
//         0 => 'Nie świadczymy'
//     ];
    private $_teryt = null;
    public $addressId = null;
    
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

		    [self::INFRASTRUCTURES, 'number', 'integerOnly' => true, 'min' => -1, 'max' => 3],
		    [self::INFRASTRUCTURES, 'required', 'message' => 'Wartość wymagana'],

		    [self::SERVICES, 'number', 'integerOnly' => true, 'min' => 0, 'max' => 2],
		    [self::SERVICES, 'required', 'message' => 'Wartość wymagana'],
		    
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
		    'net_1g_utp' => 'NET_UTP',
		    'net_1g_opt' => 'NET_OF',
		    'net_10g_utp' => 'NETX_UTP',
		    'net_10g_opt' => 'NETX_OF',
		    'phone' => 'PHONE',
		    'hfc' => 'HFC',
		    'iptv_utp' => 'IPTV_UTP',
		    'iptv_opt' => 'IPTV_OF',
		    'rfog' => 'RFOG',
		    'iptv_net_1g_utp' => 'IPTV_NET_UTP',
		    'iptv_net_1g_opt' => 'IPTV_NET_OF',
		    'iptv_net_10g_utp' => 'IPTV_NETX_UTP',
		    'iptv_net_10g_opt' => 'IPTV_NETX_OF',
		    'rfog_net_1g' => 'RFOG_NET',
		    'rfog_net_10g' => 'RFOG_NETX',
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
	
	public function getAllServices() {
	    
	    $serviceInfo = [$this->net_1g_utp, $this->net_1g_opt, $this->net_10g_utp, $this->net_10g_opt, $this->phone, $this->hfc, $this->iptv_utp, $this->iptv_opt, $this->rfog,
	        $this->iptv_net_1g_utp, $this->iptv_net_1g_opt, $this->iptv_net_10g_utp, $this->iptv_net_10g_opt, $this->rfog_net_1g, $this->rfog_net_10g
	    ];
	    $serviceInstall = [1, 4, 1, 4, 2, 4, 1, 4, 4, 1, 4, 1, 4, 1, 4];
	    $installInfo = [Utp::TYPE => $this->utp, Utp3::TYPE => $this->utp_cat3, Coax::TYPE => $this->coax, Fiber::TYPE => $this->optical_fiber];
	    
	    $countServiceInfo = count($serviceInfo);
	    $countServiceInstall = count($serviceInstall);
	    $array = [];
	    if ($countServiceInfo == $countServiceInstall && $countServiceInstall == count(self::SERVICES)) {
	        $countUtp = $countUtp3 = $countCoax = $countFiber = null;
	        for ($i = 0; $i <= $countServiceInfo - 1; $i++) {
	            if ($installInfo[$serviceInstall[$i]] == -1) { //gdy instalację robi szczurek
	                if ($serviceInstall[$i] == Utp::TYPE) {
	                    //TODO będą montaże zamiast instalacji
	                    if (!$countUtp) $countUtp = Installation::find()->where(['type_id' => Utp::TYPE, 'address_id' => $this->addressId])->count(); 
                        $array[self::SERVICES[$i]] = ['service_id' => $i + 1, 'service_info' => $serviceInfo[$i], 'install_id' => $serviceInstall[$i], 'install_info' => $countUtp];
                        continue;
	                } elseif ($serviceInstall[$i] == Utp3::TYPE) {
                        if (!$countUtp3) $countUtp3 = Installation::find()->where(['type_id' => Utp3::TYPE, 'address_id' => $this->addressId])->count();
                        $array[self::SERVICES[$i]] = ['service_id' => $i + 1, 'service_info' => $serviceInfo[$i], 'install_id' => $serviceInstall[$i], 'install_info' => $countUtp3];
                        continue;
	                } elseif ($serviceInstall[$i] == Coax::TYPE) {
	                    if (!$countCoax) $countCoax = Installation::find()->where(['type_id' => Coax::TYPE, 'address_id' => $this->addressId])->count();
                        $array[self::SERVICES[$i]] = ['service_id' => $i + 1, 'service_info' => $serviceInfo[$i], 'install_id' => $serviceInstall[$i], 'install_info' => $countCoax];
                        continue;
	                } elseif ($serviceInstall[$i] == Fiber::TYPE) {
	                    if (!$countFiber) $countFiber = Installation::find()->where(['type_id' => Fiber::TYPE, 'address_id' => $this->addressId])->count();
                        $array[self::SERVICES[$i]] = ['service_id' => $i + 1, 'service_info' => $serviceInfo[$i], 'install_id' => $serviceInstall[$i], 'install_info' => $countFiber];
                        continue;
	                }
	            }
                $array[self::SERVICES[$i]] = ['service_id' => $i + 1, 'service_info' => $serviceInfo[$i], 'install_id' => $serviceInstall[$i], 'install_info' => $installInfo[$serviceInstall[$i]]];   
            }
	            
            return $array;
        }
        
	    return false;
	}
	
	public function getRmqServices() {
	    
	    //service_info -> 0 - nie świadczymy, 1 - świadczymy, 2 - świadczymy, ewentualnie...
	    //install_info -> 0 - brak, 1.. - liczba przewodów danego typu, -2 kontakt z serwisem
	    
	    $allServices = $this->getAllServices();
	    $rmq = [];
	    foreach ($allServices as $service) {
	        if ($service['service_info'] <> 0) {
	            if ($service['install_info'] > 0) $rmqInstall = 1; 
	            elseif ($service['install_info'] == 0) $rmqInstall = 2; 
	            elseif ($service['install_info'] == -2) $rmqInstall = 3; 
	            $rmq[] = ['id' => $service['service_id'], 'name' => self::SERVICES[$service['service_id'] - 1], 'install' => $rmqInstall, 'priority' => $service['service_info']];
	        }
	    }
	    
	    return $rmq;
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
