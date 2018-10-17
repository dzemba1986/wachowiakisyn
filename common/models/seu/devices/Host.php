<?php

namespace common\models\seu\devices;

use common\models\seu\devices\traits\OneLink;
use common\models\soa\Connection;
use yii\helpers\ArrayHelper;

/**
 * @property integer $technic
 * @property backend\models\Connection[] $connections
 */

abstract class Host extends Device {
    
    use OneLink;
    
	const TYPE = 5;
	
	private $connectionsTypeNameToSoaId = NULL;
	private $connectionsType = NULL;
	
	public function attributes() {
	    
	    return ArrayHelper::merge(
	        parent::attributes(),
	        [
	            'technic',
	        ]
        );
	}
	
	public function rules() {
	    
	    return ArrayHelper::merge(
	        parent::rules(),
	        [
	            ['technic', 'integer'],
	            ['technic', 'in', 'range' => [1, 2]],
	            ['technic', 'required', 'message' => 'Wartość wymagana'],
	        ]
        );
	}
	
	public static function instantiate($row) {
	    
	    if ($row['type_id'] == Host::TYPE) {
	        
	        if ($row['technic'] == HostEthernet::TECHNIC) return new HostEthernet(); 
	        elseif ($row['technic'] == HostRfog::TECHNIC) return new HostRfog();
	    }
	}
	
	public function scenarios() {
	    
	    $scenarios = parent::scenarios();
	    $scenarios[self::SCENARIO_CREATE] = ['technic'];
	    
	    return $scenarios;
	}
	
	public function attributeLabels() {
	    
        return ArrayHelper::merge(
            parent::attributeLabels(),
            [
                'technic' => 'Technologia'
            ]
        ); 
	}
	
	public function getConnections() {

	    return $this->hasMany(Connection::className(), ['host_id' => 'id']);
	}
	
	public function getConnectionsTypeNameToSoaId() {
	    
	    if (is_null($this->connectionsTypeNameToSoaId)) {
	        $this->connectionsTypeNameToSoaId = (new \yii\db\Query())
	        ->select(['connection.id', 'connection_type.name', 'soa_id'])
	        ->from('connection')
	        ->leftJoin('connection_type', 'connection_type.id = type_id')
	        ->where(['host_id' => $this->id])
	        ->all();
	    }
	    
	    return $this->connectionsTypeNameToSoaId;
	}
	
	public function getConnectionsType() : array {
	    
	    if (is_null($this->connectionsType)) {
	        foreach ($this->getConnections()->select('connection.type_id')->asArray()->all() as $connection) {
	            $this->connectionsType[] = $connection['type_id'];
	        }
	    }
	    
	    return $this->connectionsType;
	}
}