<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "lokalizacja".
 *
 * @property integer $id
 * @property string $ulic
 * @property string $blok
 * @property string $mieszkanie
 * @property string $klatka
 * @property string $nazwa_inna
 */
class AddressOld extends \yii\db\ActiveRecord
{
    public static function getDb() {
        
        return \Yii::$app->dbLP; 
    }
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'Lokalizacja';
    }
    
    public function getFullAddress(){
        
        $arStreetMap = [
            '01957' => 'Bóżnicza',
            '03269' => 'Czarna Rola',
            '09439' => 'Kosmonautów',
            '12272' => 'Marcelińska',
            '13631' => 'Na Murawie',
            '13989' => 'Naramowicka',
            '15776' => 'Pasterska',
            '16636' => 'Pod Lipami',
            '17923' => 'Przyjaźni',
            '19232' => 'Kondratija Rylejewa',
            '22907' => 'Towarowa',
            '23990' => 'Wichrowe Wzgórze',
            '24263' => 'Wilczak',
            '26323' => 'Zwycięstwa',
        ];
		
		return $arStreetMap[$this->ulic].' '.$this->blok.$this->klatka.'/'.$this->mieszkanie.$this->nazwa_inna;
	}
}
