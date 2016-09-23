<?php

namespace backend\models;

use Yii;
use backend\models\Device;
/**
 * This is the model class for table "tbl_model".
 *
 * The followings are the available columns in table 'tbl_model':
 * @property integer $device
 * @property string $podsiec
 * @property integer $ip
 * @property integer $main
 */

class IpOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Adres_ip';
    }
}
