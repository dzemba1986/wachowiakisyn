<?php

namespace frontend\modules\crm\models\forms;

use common\models\crm\DeviceTask;
use yii\base\Model;

class CreateMonitoringTask extends Model
{
    public $device_id;
    public $description;

    public function rules() {
        
        return [
        	['device_id', 'integer'],
        	['device_id', 'required', 'message'=>'Wartość wymagana'],
        		
        	['description', 'required', 'message'=>'Wartość wymagana'],
        ];
    }

    public function attributeLabels() {
        
        return [
            'device_id' => 'Urządzenie',
        	'description' => 'Opis'
        ];
    }

    public function create() {
        
    	if ($this->validate()) {
    		$task = new DeviceTask(['scenario' => DeviceTask::SCENARIO_CREATE]);
    		
    		$task->description = $this->description;
    		$task->device_id = $this->device_id;
    		$task->editable = true;
    		$task->type_id = 5;
    		$task->category_id = 5;
    		
    		if ($task->save()) {
    			return $task;
    		} else 
    			return print_r($task->errors);
    	}
    	
    	return null;
    }
}
