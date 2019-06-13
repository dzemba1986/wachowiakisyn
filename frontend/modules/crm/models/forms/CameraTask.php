<?php

namespace frontend\modules\crm\models\forms;

use common\models\crm\DeviceTask;
use common\models\crm\Task;
use yii\base\Model;

class CameraTask extends Model {
    
    public $device_id;
    public $desc;

    public function rules() {
        
        return [
        	['device_id', 'filter', 'filter' => 'intval'],
        	['device_id', 'integer'],
        	['device_id', 'required', 'message' => 'Wartość wymagana'],
        		
        	['desc', 'required', 'message' => 'Wartość wymagana'],
        ];
    }

    public function attributeLabels() {
        
        return [
            'device_id' => 'Urządzenie',
        	'desc' => 'Opis'
        ];
    }

    public function create() {
        
    	if ($this->validate()) {
    		$task = new DeviceTask([
    		    'scenario' => Task::SCENARIO_CREATE
    		]);
    		$task->desc = $this->desc;
    		$task->device_id = $this->device_id;
    		$task->category_id = 10; //usterka
    		$task->receive_by = 1; //dla serwisu
    		
//     		$task->validate();
//     		$task->beforeSave(true);
//     		var_dump($task->beforeSave(true)); exit();
    		if ($task->save()) return $task;
    	}
    	
    	return null;
    }
}
