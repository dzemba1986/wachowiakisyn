<?php
namespace common\models\rmq;

use yii\behaviors\AttributeBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\Json;

/**
 * @property integer $id
 * @property integer $event_id
 * @property integer $case_id
 * @property integer $event_type
 * @property string $create_at
 * @property string $message
 */

abstract class Job extends ActiveRecord {
    
    const TYPE = 0;
    
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
    
    public function fields() {
        
        return [
            'event_id',
            'case_id',
            'event_type',
            'create_at',
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
            
            ['create_at', 'date', 'format' => 'php:c'],
            ['create_at', 'required', 'message' => 'Wartość wymagana'],

//             ['message', 'required', 'message' => 'Wartość wymagana'],
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
    
    public function behaviors() {
        
        return [
//             [
//                 'class' => AttributeBehavior::class,
//                 'attributes' => [
//                     self::EVENT_BEFORE_VALIDATE => 'event_id',
//                 ],
//                 'value' => function () {
//                     $max = Job::find()->select(['max' => new Expression('max(event_id)')])->where(['and', ['case_id' => $this->case_id], '(event_id % 2) = 1'])->asArray()->one()['max'];
//                     if ($max) return $max + 2;
//                     else return 1;
//                 }
//             ],
//             [
//                 'class' => TimestampBehavior::class,
//                 'attributes' => [
//                     self::EVENT_BEFORE_VALIDATE => ['create_at'],
//                 ],
//                 'value' => date('c'),
//             ],
            [
                'class' => AttributeBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_VALIDATE => 'event_type',
                ],
                'value' => static::TYPE,
            ],
        ];
    }
    
    public function afterValidate() {
        
        $extraProperties = array_intersect_key(get_object_vars($this), array_flip(array_diff($this->fields(), ['event_id', 'case_id', 'event_type', 'create_at'])));
        $this->message = Json::encode($extraProperties);
    }
    
    public function setParams() {
        
        $max = Job::find()->select(['max' => new Expression('max(event_id)')])->where(['and', ['case_id' => $this->case_id], '(event_id % 2) = 1'])->asArray()->one()['max'];
        if ($max) $this->event_id = $max + 2;
        else $this->event_id = 1;

        $this->create_at = date('c');
    }
}