<?php

namespace common\models\crm;

use common\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;

/**
 * @property integer $id
 * @property string $create_at
 * @property string $desc
 * @property integer $create_by
 * @property integer $comment_id
 * @property integer $task_id
 */
class Comment extends ActiveRecord {
    
    public static function tableName() {
        
    	return '{{comment}}';
    }
	
    public function rules() {
        
        return [	
            ['desc', 'string'],
            
            ['task_id', 'integer'],
        		
            ['comment_id', 'integer'],
            
            [['create_at', 'task_id', 'comment_id', 'create_by', 'desc'], 'safe'],
        ];
    }
    
    public function attributeLabels() {
        
        return [
            'id' => 'ID',
        	'create_at' => 'Utworzono',
            'create_by' => 'Autor',
            'desc' => 'Komentarz'
        ];
    }
    
    public function behaviors() {
        
        return [
            [
                'class' => TimestampBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['create_at'],
                ],
                'value' => new Expression('NOW()::timestamp(0)'),
            ],
            [
                'class' => BlameableBehavior::class,
                'attributes' => [
                    self::EVENT_BEFORE_INSERT => ['create_by'],
                ],
                'value' => \Yii::$app->user->id,
            ],
        ];
    }
    
    public function beforeSave($insert) {
        
        if (!parent::beforeSave($insert)) return false;
        if ($insert) Task::updateAll(['status' => 2], ['id' => $this->task_id, 'status' => 0]);

        return true;
    }
    
    public function getCreateBy() {

        return $this->hasOne(User::class, ['id' => 'create_by']);
    }
}