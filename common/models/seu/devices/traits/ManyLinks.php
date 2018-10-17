<?php

namespace common\models\seu\devices\traits;

use common\models\seu\Link;

trait ManyLinks {
    
    public function getParentsLinks() {
        
        return $this->hasMany(Link::className(), ['device' => 'id']);
    }
}
?>