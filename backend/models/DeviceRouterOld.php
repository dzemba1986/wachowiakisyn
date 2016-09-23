<?php

namespace backend\models;

use Yii;

class DeviceRouterOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Router';
    }
    
    public function getModelModel(){
    
    	//urządzenie ma jeden typ
    	return $this->hasOne(ModelOld::className(), ['id'=>'model']);
    }
}
