<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace common\models\rmq;

use Yii;

/**
 * Class Json
 *
 * @author Roman Zhuravlev <zhuravljov@gmail.com>
 */
class EventCreator {
    
    /**
     * @param array $data
     * @return mixed
     */
    public function fromArray($data) {
        
        if (!is_array($data)) {
            return $data;
        }
        
        if (!isset($data['class'])) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->fromArray($value);
            }
            
            return $result;
        }
        
        $config = ['class' => $data['class']];
        unset($data[$this->classKey]);
        foreach ($data as $property => $value) {
            $config[$property] = $this->fromArray($value);
        }
        
        return Yii::createObject($config);
    }
}
