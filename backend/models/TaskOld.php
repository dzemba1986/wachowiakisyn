<?php

namespace backend\models;

use Yii;


class TaskOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbLP; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'modyfications';
    }
    
    public function getModelAddress(){

        //Installation jest robiona na jednym Address
        return $this->hasOne(AddressOld::className(), ['id'=>'mod_loc']);
    }
    
    public function getModelConnection(){

        //Installation jest robiona na jednym Address
        return $this->hasOne(ConnectionOld::className(), ['modyfication'=>'mod_id']);
    }
}
