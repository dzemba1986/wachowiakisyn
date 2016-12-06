<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "connection".
 *
 * @property integer $id
 * @property string $ara_id
 * @property string $start_date
 * @property string $add_date
 * @property string $address
 * @property string $localization
 * @property string $switch
 * @property string $port
 * @property integer $switch_loc
 * @property integer $switch_loc_str
 * @property integer $mac
 * @property integer $service
 * @property integer $moved_phone
 * @property integer $speed
 * @property integer $service_configuration
 * @property integer $informed
 * @property integer $service_activation
 * @property integer $veryfication_method
 * @property integer $configuration_user
 * @property string $payment_activation
 * @property string $resignation_date
 * @property string $phone
 * @property string $phone2
 * @property integer $phone3
 * @property integer $installation_date
 * @property string $installation_user
 * @property string $info
 * @property string $last_modyfication
 * @property string $info_boa
 * @property integer $modyfication
 */
class ConnectionOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbLP; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Connections';
    }
    
    public function getModelBoa(){
    
    	//Connection ma tylko 1 Address
    	return $this->hasOne(BoaOld::className(), ['connection_id'=>'id']);
    }
}
