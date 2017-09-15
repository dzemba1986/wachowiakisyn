<?php

namespace app\models;

use yii\db\ActiveRecord;
/**
 * This is the model class for table "ulic".
 *
 * The followings are the available columns in table 'ulic':
 * @property integer $id
 * @property string $woj
 * @property string $pow
 * @property string $gmi
 * @property string $rodz_gmi
 * @property string $sym
 * @property string $sym_ul
 * @property string $cecha
 * @property string $nazwa_1
 * @property string $nazwa_2
 */
class Ulic extends ActiveRecord
{
	public static function tableName()
	{
		return '{{ulic}}';
	}
	
}
