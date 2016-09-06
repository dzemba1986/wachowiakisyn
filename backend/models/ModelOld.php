<?php

namespace backend\models;

use Yii;
use yii\data\ActiveDataProvider;
use backend\models\Device;
/**
 * This is the model class for table "tbl_model".
 *
 * The followings are the available columns in table 'tbl_model':
 * @property integer $id
 * @property string $name
 * @property integer $port_count
 * @property integer $type
 * @property integer $manufacturer

 */
class ModelOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'model';
    }
}
