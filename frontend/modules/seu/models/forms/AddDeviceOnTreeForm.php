<?php

namespace frontend\modules\seu\models\forms;

use common\models\seu\Link;
use common\models\seu\devices\Device;
use common\models\seu\network\Ip;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use backend\modules\address\models\Address;

class AddDeviceOnTreeForm extends Model {
    
    public $parentId;
    public $parentPort;
    public $childId;
    public $childPort;
    public $vlan;
    public $subnet;
    public $ip;
    
    public function rules() {
        
        return [
            [['parentId', 'parentPort', 'childId', 'childPort', 'vlan', 'subnet'], 'integer'],
            [['parentId', 'parentPort', 'childId', 'childPort'], 'required', 'message' => 'Wartość wymagana'],
            
            ['ip', 'string'],
            
            [['patentId', 'parentPort', 'childId', 'childPort', 'vlan', 'subnet', 'ip'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        
        return [
            'parentId' => 'Urządzenie nadrzedne',
            'childId' => 'Urządzenie',
            'parentPort' => 'Port nadrządny',
            'childPort' => 'Port urzadzenia',
            'vlan' => 'Vlan',
            'subnet' => 'Podsieć',
            'ip' => 'Ip',
        ];
    }
    
    public function add() {
        
        if ($this->validate()) {
            $parentDevice = Device::find()->where(['id' => $this->parentId])->asArray()->one();
            
            $childDevice = Device::findOne($this->childId);
            $childDevice->name = Address::findOne($parentDevice['address_id'])->toString(true);
            $childDevice->address_id = $parentDevice['address_id'];
            $childDevice->addOnTree();
            
            $link = new Link();
            $link->device = $this->childId;
            $link->port = $this->childPort;
            $link->parent_device = $this->parentId;
            $link->parent_port = $this->parentPort;
            
            if ($childDevice->getCanHasIp()) {
                $ip = new Ip();
                $ip->ip = $this->ip;
                $ip->main = true;
                $ip->subnet_id = $this->subnet;
                $ip->device_id = $this->childId;
            }
            
            $isValid = $childDevice->validate();
            $isValid = $link->validate() && $isValid;
            if ($childDevice->getCanHasIp()) $isValid = $ip->validate() && $isValid;
            
            try {
                if ($isValid) {
                    $transaction = Yii::$app->getDb()->beginTransaction();
                    $childDevice->save(false);
                    $link->save(false);
                    if ($childDevice->getCanHasIp()) $ip->save(false);
                } else throw new Exception('Błąd zapisu');
                
                $transaction->commit();
                return 1;
            } catch (Exception $e) {
                $transaction->rollBack();
                echo $e->getMessage();
            }
        }
    }
}