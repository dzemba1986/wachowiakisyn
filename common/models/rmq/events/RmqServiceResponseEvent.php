<?php
namespace common\models\rmq\events;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use common\models\address\Address;
use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class RmqServiceResponseEvent extends RmqEvent implements \JsonSerializable{
    
    const TYPE = 2;
    
    public $t_woj;
    public $t_pow;
    public $t_gmi;
    public $t_rodz;
    public $t_miasto;
    public $t_ulica;
    public $ulica_prefix;
    public $ulica;
    public $dom;
    public $dom_szczegol;
    public $lokal;
    public $lokal_szczegol;
    
    public function __construct($request) {
        
        parent::__construct();
        
        $this->event_id = $request->event_id + 1;
        $this->case_id = $request->case_id;
        $this->t_woj = $request->t_woj;
        $this->t_pow = $request->t_pow;
        $this->t_gmi = $request->t_gmi;
        $this->t_rodz = $request->t_rodz;
        $this->t_miasto = $request->t_miasto;
        $this->t_ulica = $request->t_ulica;
        $this->ulica_prefix = $request->ulica_prefix;
        $this->ulica = $request->ulica;
        $this->dom = $request->dom;
        $this->dom_szczegol = $request->dom_szczegol;
        $this->lokal = $request->lokal;
        $this->lokal_szczegol = $request->lokal_szczegol;
    }
    
    public function rules() {
        
        return ArrayHelper::merge(
            parent::rules(),
            [
                ['t_woj', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_pow', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_gmi', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_rodz', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_miasto', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_miasto', 'required', 'message' => 'Wartość wymagana'],
                
                ['t_ulica', 'required', 'message' => 'Wartość wymagana'],
                
                ['ulica_prefix', 'required', 'message' => 'Wartość wymagana'],
                
                ['ulica', 'required', 'message' => 'Wartość wymagana'],
                
                ['dom', 'string', 'min' => 1, 'max' => 10],
                ['dom', 'required', 'message' => 'Wartość wymagana'],
                ['dom', 'trim'],
                
                ['dom_szczegol', 'string', 'min' => 1, 'max' => 50],
                ['dom_szczegol', 'default', 'value' => ''],
                ['dom_szczegol', 'filter', 'filter' => 'strtoupper'],
                ['dom_szczegol', 'trim'],
                
                ['lokal', 'string', 'min' => 1, 'max' => 10],
                ['lokal', 'default', 'value' => ''],
                ['lokal', 'trim'],
                
                ['lokal_szczegol', 'string', 'min' => 1, 'max' => 50],
                ['lokal_szczegol', 'default', 'value' => ''],
                ['lokal_szczegol', 'filter', 'filter'=>'strtoupper'],
                ['lokal_szczegol', 'trim'],
            ]
        );
    }
    
//     public function toObject($json) {
        
//         parent::toObject($json);
        
//         $this->message = $json['address']['teryt_woj'];
        
//         if ($this->validate()) return $this;
//         else return false;
//     }
    
    public function beforeValidate() {
        
        $json = $this->getAddress()->serviceRange->rmqServices;
        $this->message = Json::encode($json);
        
        return parent::beforeValidate();
    }
    
    public function behaviors() {
        
        return [
//             [
//                 'class' => AttributeBehavior::class,
//                 'attributes' => [
//                     self::EVENT_BEFORE_VALIDATE => 'event_id',
//                 ],
//                 'value' => function () {
//                     $max = RmqEvent::find()->select(['max' => new Expression('max(event_id)')])->where(['case_id' => $this->case_id])->asArray()->one()['max'];
//                     if ($max) return $max + 1;
//                     else return 1;
//                 }
//             ],
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => ['create_at'],
                ],
                'value' => new Expression('NOW()::timestamp(0)'),
            ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => 'event_type',
                ],
                'value' => self::TYPE,
            ],
        ];
    }
    
    public function afterSave($insert, $changeAttributes) {
        
        if ($insert) {
            $queue = \Yii::$app->queueSeuToSoa;
            $connection = new AMQPStreamConnection($queue->host, $queue->port, $queue->user, $queue->password);
            $channel = $connection->channel();
            $channel->queue_declare($queue->queueName, false, true, false, false, false, new AMQPTable(['x-max-priority' => 10]));
            
            $json = json_encode($this);
            $msg = new AMQPMessage($json);
            $channel->basic_publish($msg, '', $queue->queueName);
            $channel->close();
            $connection->close();
        }
    }
    
    public function jsonSerialize() {
        
        return [
            'event_id' => $this->event_id,
            'case_id' => $this->case_id,
            'event_type' => $this->event_type,
            'create_at' => $this->create_at,
            'services' => $this->message,
            
        ];
    }
    
    private function getAddress() {
        
        $address = new Address();
        $address->t_woj = $this->t_woj;
        $address->t_pow = $this->t_pow;
        $address->t_gmi = $this->t_gmi;
        $address->t_rodz = $this->t_rodz;
        $address->t_miasto = $this->t_miasto;
        $address->t_ulica = $this->t_ulica;
        $address->ulica_prefix = $this->ulica_prefix;
        $address->ulica = $this->ulica;
        $address->dom = $this->dom;
        $address->dom_szczegol = $this->dom_szczegol;
        $address->lokal = $this->lokal;
        $address->lokal_szczegol = $this->lokal_szczegol;

        return $address;
    }
    
}

