<?php
namespace backend\models;

use yii\db\ActiveQuery;

class DeviceQuery extends ActiveQuery
{
	public $type_id;

	public function prepare($builder)
	{
		if ($this->type_id !== null) {
			$this->andWhere(['type_id' => $this->type_id]);
		}
		return parent::prepare($builder);
	}
}
?>
