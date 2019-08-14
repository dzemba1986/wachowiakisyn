<?php
namespace app\versions\v1\models;

use common\models\address\Address;

class AddressResource extends Address {
    
    public function fields() {
        
        return[
            'ulica_prefix',
            'ulica',
            'dom',
            'dom_szczegol',
            'lokal',
            'lokal_szczegol',
            'pietro'
        ];
    }
}

