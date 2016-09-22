<?php

namespace backend\models;

use Yii;


class DeviceHostOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'host';
    }
    
    public function getModelConnection(){
    
    	//urządzenie ma jeden typ
    	return $this->hasOne(ConnectionOld::className(), ['id'=>'con_id']);
    }
}
