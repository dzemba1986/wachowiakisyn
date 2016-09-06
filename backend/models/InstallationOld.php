<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "installations".
 *
 * @property integer $installation_id
 * @property string $address
 * @property string $localization
 * @property string $wire_length
 * @property string $wire_installation_date
 * @property string $socket_installation_date
 * @property string $wire_installer
 * @property string $socket_installer
 * @property string $type
 * @property string $connection_id
 * @property string $invoiced
 */
class InstallationOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbLP; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'installations';
    }
}
