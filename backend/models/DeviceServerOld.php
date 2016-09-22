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
        return 'serwer';
    }
}
