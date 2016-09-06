<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;

class DeviceSwitchRejonOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'switch_rejon';
    }
}
