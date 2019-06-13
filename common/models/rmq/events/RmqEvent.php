<?php
namespace common\models\rmq\events;

use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property integer $event_id
 * @property integer $case_id
 * @property integer $event_type
 * @property string $create_at
 * @property string $message
 */

abstract class RmqEvent extends ActiveRecord implements RmqEventInterface {
    
    
    public static function tableName() {
        
        return '{{rmq_event}}';
    }

    public static function instantiate($row) {
        
    }
    
    public function attributes() {
        
        return [
            'id',
            'event_id',
            'case_id',
            'event_type',
            'create_at',
            'message',
        ];
    }
    
    public function rules() {
        
        return [
            ['event_id', 'filter', 'filter' => 'intval'],
            ['event_id', 'integer'],
            ['event_id', 'required', 'message' => 'Wartość wymagana'],
            
            ['case_id', 'filter', 'filter' => 'intval'],
            ['case_id', 'integer'],
            ['case_id', 'required', 'message' => 'Wartość wymagana'],
            
            ['event_type', 'filter', 'filter' => 'intval'],
            ['event_type', 'integer'],
            ['event_type', 'required', 'message' => 'Wartość wymagana'],
            
//             ['create_at', 'date', 'format' => ''], //TODO
            ['create_at', 'required', 'message' => 'Wartość wymagana'],

            ['message', 'required', 'message' => 'Wartość wymagana'],
        ];
    }
    
    public function attributeLabels() {
        
        return [
            'event_id' => 'ID komunikatu',
            'case_id' => 'ID sprawy',
            'event_type' => 'Typ komunikatu',
            'create_at' => 'Utworzono',
        ];
    }
    
//     public function behaviors() {
        
//         return [
//             [
//                 'class' => AttributeBehavior::class,
//                 'attributes' => [
//                     self::EVENT_BEFORE_INSERT => 'event_id',
//                 ],
//                 'value' => function () {
//                     $max = RmqEvent::find()->select(['max' => new Expression('max(event_id)')])->where(['case_id' => $this->case_id])->asArray()->one()['max'];
//                     if ($max) return $max + 1;
//                     else return 1;
//                 }
//             ],
//             [
//                 'class' => TimestampBehavior::class,
//                 'attributes' => [
//                     self::EVENT_BEFORE_INSERT => ['create_at'],
//                 ],
//                 'value' => new Expression('NOW()::timestamp(0)'),
//             ],
//         ];
//     }
    
    public function toObject($json) {
        
        $this->event_id = $json['event_id'];
        $this->case_id = $json['case_id'];
        $this->event_type = $json['event_type'];
        $this->create_at = $json['create_at'];
    }
}

