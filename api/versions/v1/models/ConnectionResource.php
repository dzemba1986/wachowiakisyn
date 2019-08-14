<?php
namespace app\versions\v1\models;

use common\models\soa\Connection;
use yii\helpers\Url;
use yii\web\Link;
use yii\web\Linkable;

class ConnectionResource extends Connection implements Linkable {
    
    public function fields() {
        
        return [
//             'id',
            'SOA' => 'soa_id'
        ];
    }
    
    public function extraFields() {
        
        return [
            'address'
        ];
    }
    
    public function getLinks() {
        
        return [
            Link::REL_SELF => Url::to(['connection/view', 'id' => $this->id], true),
            'edit' => Url::to(['connection/view', 'id' => $this->id], true),
            'index' => Url::to(['connection'], true),
        ];
    }
    
    public function getAddress() {
        
        return $this->hasOne(AddressResource::class, ['id' => 'address_id'])->select('id, t_ulica, ulica_prefix, ulica, dom, dom_szczegol, lokal, lokal_szczegol');
    }
}

