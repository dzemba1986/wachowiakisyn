<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "subnet".
 *
 * The followings are the available columns in table 'subnet':
 * @property integer $id
 * @property string $address
 * @property integer $netmask
 * @property integer $vlan
 * @property string $opis
 * @property integer $dhcp
 * @property integer $dhcp_group
 */
class DhcpValueOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbSeu; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'dhcp_group_option';
    }
}
