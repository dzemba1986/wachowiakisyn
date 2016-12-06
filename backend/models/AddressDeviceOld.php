<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "lokalizacja".
 *
 * @property integer $id
 * @property string $osiedle
 * @property string $nr_bloku
 * @property string $klatka
 * @property string $ulic
 */

class AddressDeviceOld extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
	public static function getDb() {
	
		return \Yii::$app->dbSeu;
	}
	
    public static function tableName()
    {
        return 'Lokalizacja';
    }
    
}
