<?php
namespace common\models\rmq\events;

interface RmqEventInterface {
    
    public function toObject($json);
}

