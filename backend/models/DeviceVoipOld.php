<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;


class DeviceVoipOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Bramka_voip';
    }
    
    public function getModelModel(){
    
    	//urządzenie ma jeden typ
    	return $this->hasOne(ModelOld::className(), ['id'=>'model']);
    }
}
