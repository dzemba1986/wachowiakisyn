<?php

namespace common\models\seu\devices\traits;

use common\models\seu\Link;
use yii\db\ArrayExpression;

trait ParentDevice {
    
    public $portIndex = NULL;
    private $portNumber = NULL;
    private $portName = NULL;
    private $modelId = NULL;
    private $ports = NULL;
    
    public function getChildrenLinks() {
        
        return $this->hasMany(Link::className(), ['parent_device' => 'id']);
    }
    
    public function isParent() : bool {
        
        return Link::find()->where(['parent_device' => $this->id])->count() > 0 ? TRUE : FALSE;
    }
    
    private function getModelId() : int {
        
        if (is_null($this->modelId)) $this->modelId = $this->getModel()->select('id')->asArray()->one()['id'];
        
        return $this->modelId;
    }
    
    private function getPorts() : ArrayExpression {
        
        if (is_null($this->ports)) $this->ports = $this->getModel()->select('port')->one()->port;
        
        return $this->ports;
    }
    
    public function getPortNumber() : int {
        
        if (is_null($this->portNumber)) {
            $modelId = $this->getModelId();
            
            $this->portNumber = $modelId <> 6 ? $this->portIndex + 1 : $this->portIndex + 25;
        }
        
        return $this->portNumber;
    }
    
    public function getPortName() : string {
        
        if (is_null($this->portName)) $this->portName = $this->getPorts()[$this->portIndex];
        
        return $this->portName;
    }
}