<?php
namespace app\versions\v1\models;

use common\models\soa\Connection;

class ConnectionResource extends Connection {
    
    public function fields() {
        
        return[
            'id',
            'soa_id'
        ];
    }
    
    public function extraFields() {
        
        return [
            'address'
        ];
    }
    
    public function getAddress() {
        
        return $this->hasOne(AddressResource::className(), ['id' => 'address_id'])->select('id, t_ulica, ulica_prefix, ulica, dom, dom_szczegol, lokal, lokal_szczegol');
    }
}

