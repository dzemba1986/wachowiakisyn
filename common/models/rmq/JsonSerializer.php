<?php

namespace common\models\rmq;

use common\models\rmq\cases\connect\ConnectJob;
use common\models\rmq\cases\services\ServiceRequestJob;
use Yii;
use yii\base\BaseObject;
use yii\base\InvalidConfigException;
use yii\helpers\Json;
use yii\queue\serializers\SerializerInterface;

class JsonSerializer extends BaseObject implements SerializerInterface {
    /**
     * @var string
     */
    public $classKey = 'event_type';
    /**
     * @var int
     */
    public $options = 0;


    /**
     * @inheritdoc
     */
    public function serialize($job)
    {
        return Json::encode($this->toArray($job), $this->options);
    }

    /**
     * @inheritdoc
     */
    public function unserialize($serialized)
    {
        return $this->fromArray(Json::decode($serialized));
    }

    /**
     * @param mixed $data
     * @return array|mixed
     * @throws InvalidConfigException
     */
    protected function toArray($data)
    {
        if (is_object($data)) return $data->toArray();
        
        if (is_array($data)) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->toArray($value);
            }

            return $result;
        }
        
        return $data;
    }

    /**
     * @param array $data
     * @return mixed
     */
    protected function fromArray($data)
    {
        if (!is_array($data)) {
            return $data;
        }
        
        if (!isset($data[$this->classKey])) {
            $result = [];
            foreach ($data as $key => $value) {
                $result[$key] = $this->fromArray($value);
            }
            
            return $result;
        }
        
        switch ($data['event_type']) {
            case ServiceRequestJob::TYPE:
                $config = ['class' => ServiceRequestJob::class];
                break;
            case ConnectJob::TYPE:
                $config = ['class' => ConnectJob::class];
                break;
        }
        
        foreach ($data as $property => $value) {
            $config[$property] = $this->fromArray($value);
        }
        
        return Yii::createObject($config);
    }
}
