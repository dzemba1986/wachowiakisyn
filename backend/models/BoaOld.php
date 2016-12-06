<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "boa".
 *
 * @property integer $connection_id
 * @property date $ara_sync
 * @property integer $user_id
 */
class BoaOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbLP; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Boa';
    }
}
