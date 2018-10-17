<?php

namespace common\models\seu\devices;

use yii\data\ActiveDataProvider;

class DeviceSearch extends BusinessDevice {
    
	public function rules() {
	    
	    return [
            [['status', 'address_id', 'mac', 'name', 'type_id', 'desc', 'model_id', 'manufacturer_id', 'serial'], 'safe']
        ];
	}
	
	public function search($params) {
	    
		$query = self::find();
	
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
			'pagination' => ['defaultPageSize' => 100, 'pageSizeLimit' => [1,2000]],
		]);
		
		$query->joinWith(['manufacturer', 'model', 'type']);
		
		$dataProvider->setSort([
			'attributes' => [
				'status',
				'mac',
			    'serial',
				'device.type_id',
                'model_id',
                'device.manufacturer_id',
			]
		]);
		
		if (!($this->load($params) && $this->validate())) {
			return $dataProvider;
		}
		
		if ($this->mac) {
		    $mac = str_replace([':', '.', '-'], '', $this->mac);
		    
		    $count = strlen($mac);
		    for($i=0;$count>$i;$i++){
		        
		        if($i%2==1){
		            $newMac .=  $mac[$i].':';
		        }else{
		            $newMac .=  $mac[$i];
		        }
		    }
		}
	
		$query->andFilterWhere([
			'id' => $this->id,
			'status' => $this->status,
		    'device.type_id' => $this->type_id,
			'address_id' => $this->address_id,
            'device.manufacturer_id' => $this->manufacturer_id,
            'model_id' => $this->model_id,
		]);
	
		$query->andFilterWhere(['like', 'serial', $this->serial])
            ->andFilterWhere(['like', '"mac"::text' , $newMac]);    
	
		return $dataProvider;
	}
}