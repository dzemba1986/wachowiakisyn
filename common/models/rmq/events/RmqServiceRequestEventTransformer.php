<?php
namespace common\models\rmq\events;

use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;
use Karriere\JsonDecoder\Bindings\AliasBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use common\models\address\Address;

class RmqServiceRequestEventTransformer implements Transformer {
    
    public function register(ClassBindings $classBindings) {
        
        $classBindings->register(new FieldBinding('address', 'address', Address::class));
        $classBindings->register(new AliasBinding('', 'event_id'));
    }
    
    public function transforms() {
        
        return RmqServiceRequestEvent::class;
    }
    
}

