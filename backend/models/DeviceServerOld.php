<?php

namespace backend\models;

use Yii;

class DeviceServerOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Serwer';
    }
    
    public function getModelModel(){
    
    	//urządzenie ma jeden typ
    	return $this->hasOne(ModelOld::className(), ['id'=>'model']);
    }
}
