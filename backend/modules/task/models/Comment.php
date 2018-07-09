<?php

namespace backend\modules\task\models;

use common\models\User;
use yii\db\ActiveRecord;

/**
 * @property integer $id
 * @property string $create
 * @property string $description
 * @property integer $user_id
 * @property integer $comment_id
 * @property integer $task_id
 */
class Comment extends ActiveRecord
{	
	const SCENARIO_UPDATE = 'update';
	
    public static function tableName(){
        
    	return '{{comment}}';
    }
	
    public function rules()
    {
        return [	
        		
            ['create', 'date', 'format' => 'yyyy-MM-dd H:i:s'],
        	['create', 'default', 'value' => date('Y-m-d H:i:s')],
        	['create', 'required', 'message'=>'Wartość wymagana'],	
            
            ['description', 'string'],
            
            ['task_id', 'integer'],
        		
            ['comment_id', 'integer'],
            
        	['user_id', 'default', 'value' => \Yii::$app->user->id],
            ['user_id', 'integer'],
            ['user_id', 'required', 'message'=>'Wartość wymagana'],
        	
            
            [['id', 'create', 'task_id', 'comment_id', 'user_id', 'description'], 'safe'],
        ];
    }
    
    public function scenarios()
    {
    	$scenarios = parent::scenarios();
    	$scenarios[self::SCENARIO_UPDATE] = ['description'];
    	
    	return $scenarios;
    }
    
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
        	'create' => 'Utworzono',
            'user_id' => 'Utworzył',
            'description' => 'Komentarz'
        ];
    }
    
    public function getUser(){

        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}