<?php
namespace common\models\seu\devices\traits;

use common\models\seu\Link;
use common\models\seu\devices\Device;

trait OneLink {
    
    private $parentLink = NULL;
    private $parentId = NULL;
    private $parent = NULL;
    private $_configParent = null;
    
    public function getParentLink() {
        
        if (is_null($this->parentLink)) $this->parentLink = $this->hasOne(Link::className(), ['device' => 'id'])->asArray()->one();
        
        return $this->parentLink; 
    }
    
    public function getParentId() {
        
        if (is_null($this->parentId)) $this->parentId = $this->getParentLink()['parent_device'];
        
        return $this->parentId;
    }
    
    public function getParent() {
        //TODO należy sprawdzić czy urządzenie jest konfigurowalnym rodzicem (np. MC nie jest)
        if (is_null($this->parent)) {
            $this->parent = Device::findOne($this->getParentId());
            if (!in_array(ParentDevice::class, class_uses($this->parent::className()))) return NULL;
            $this->parent->portIndex = $this->getParentLink()['parent_port'];
        }
        
        return $this->parent;
    }
    
    public function getConfigParent() {
        //TODO należy sprawdzić czy urządzenie jest konfigurowalnym rodzicem (np. MC nie jest)
        if (is_null($this->_configParent)) {
            $current = $this;
            while ($current->type_id <> 2) {
                $prev = $current;
                $current = Device::findOne($current->getParentId());;
            }
            
            if (!in_array(ParentDevice::class, class_uses($current::className()))) return NULL;
            $current->portIndex = $prev->getParentLink()['parent_port'];
        }
        
        return $current;
    }
}
?>