<?php

namespace common\models\crm\query;

use yii\db\ActiveQuery;

class TaskQuery extends ActiveQuery {
    
	public $type_id;
	public $columns;
    
	public function prepare($builder) {
	    
	    if (empty($this->select)) $this->select($this->columns);
		if ($this->type_id !== NULL) $this->andWhere(['task.type_id' => $this->type_id]);
		
		return parent::prepare($builder);
	}
}
?>
