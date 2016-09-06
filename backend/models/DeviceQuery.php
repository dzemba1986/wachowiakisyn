<?php
namespace backend\models;

use yii\db\ActiveQuery;

class DeviceQuery extends ActiveQuery
{
	public $type;

	public function prepare($builder)
	{
		if ($this->type !== null) {
			$this->andWhere(['type' => $this->type]);
		}
		return parent::prepare($builder);
	}
}
?>
