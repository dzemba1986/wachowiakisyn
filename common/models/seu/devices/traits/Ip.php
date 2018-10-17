<?php

namespace common\models\seu\devices\traits;

use common\models\seu\network\Ip as modelIp;

trait Ip {
    
    private $vlansToIps = [];
    private $hasIps = NULL;
    private $firstIp = NULL;
    
    public final function getIps() {
        
        return $this->hasMany(modelIp::className(), ['device_id' => 'id'])->orderBy(['main' => SORT_DESC]);
    }
    
    public final function getMainIp() {
        
        return $this->hasOne(modelIp::className(), ['device_id' => 'id'])->where(['main' => true]);
    }
    
    public final function getFirstIp() : string {
        
        if (is_null($this->firstIp)) $this->firstIp = $this->getMainIp()->select('ip')->asArray()->one()['ip'];
        
        return $this->firstIp; 
    }
    
    public final function getHasIps() : bool {
        
        if (is_null($this->hasIps)) $this->hasIps = $this->getIps()->count() > 0 ? true : false;
        
        return $this->hasIps;
    }
    
    public function getVlansToIps() {
        
        if ($this->hasIps && empty($this->vlansToIps)) {
            $this->vlansToIps = (new \yii\db\Query())
                ->select(['subnet.vlan_id', 'ip.ip'])
                ->from('ip')
                ->leftJoin('subnet', 'ip.subnet_id = subnet.id')
                ->where(['device_id' => $this->id])
                ->orderBy('main DESC')
                ->all();
        }
        
        return $this->vlansToIps;
    }
}
?>