<?php

namespace common\models\seu\devices\query;

class HostRfogQuery extends HostQuery {
    
    public function prepare($builder) {
        
        if ($this->technic !== NULL) {
            $this->andWhere(['technic' => $this->technic]);
        }
        
        return parent::prepare($builder);
    }
}
?>
