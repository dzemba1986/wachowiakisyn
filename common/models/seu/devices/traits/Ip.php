<?php

namespace common\models\seu\devices\traits;

use common\models\seu\network\Ip as modelIp;

trait Ip {
    
    private $_vlansToIps = [];
    private $_hasIps = NULL;
    private $_firstIp = NULL;
    
    public final function getIps() {
        
        return $this->hasMany(modelIp::className(), ['device_id' => 'id'])->orderBy(['main' => SORT_DESC]);
    }
    
    public final function getMainIp() {
        
        return $this->hasOne(modelIp::className(), ['device_id' => 'id'])->where(['main' => true]);
    }
    
    public final function getFirstIp() : string {
        
        if (is_null($this->_firstIp)) $this->_firstIp = $this->getMainIp()->select('ip')->asArray()->one()['ip'];
        
        return $this->_firstIp; 
    }
    
    public final function getHasIps() : bool {
        
        if (is_null($this->_hasIps)) $this->_hasIps = $this->getIps()->count() > 0 ? true : false;
        
        return $this->_hasIps;
    }
    
    public function getVlansToIps() {

        if ($this->hasIps && empty($this->_vlansToIps)) {
            $this->_vlansToIps = (new \yii\db\Query())
                ->select(['subnet.vlan_id', 'ip.ip'])
                ->from('ip')
                ->leftJoin('subnet', 'ip.subnet_id = subnet.id')
                ->where(['device_id' => $this->id])
                ->orderBy('main DESC')
                ->all();
        }
        
        return $this->_vlansToIps;
    }
}
?>