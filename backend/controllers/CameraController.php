<?php

namespace backend\controllers;

use backend\models\Camera;
use Yii;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\widgets\ActiveForm;

class CameraController extends Controller
{	
	public function actionValidation($id = null){
		 
		$modelDevice = is_null($id) ? new Camera() : $this->findModel($id);
		
		$request = Yii::$app->request;
		
		if ($request->isAjax && $modelDevice->load($request->post())) {
			
              	Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                return ActiveForm::validate($modelDevice, 'serial');
		}
	}
	
	public function actionListFromTree($q = null, $monitoring = false) {
		
		\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
		
		$out = ['results' => ['id' => '', 'alias' => '']];
		
		if (!is_null($q)) {
				
			$query = new Query();
			$query->select(['d.id', 'd.alias'])
	    	->from('device d')
	    	->where(['and', ['like', "replace(lower(d.alias), '_', ' ')", str_replace('_', ' ', strtolower($q)) . '%', false], ['<>', 'address_id', 1], ['is not', 'status', null], ['d.type_id' => Camera::TYPE]])
	    	->limit(50)->orderBy('d.alias');
	    	
    		$command = $query->createCommand();
    		$data = $command->queryAll();
    		$out['results'] = array_values($data);
		}
		
		return $out;
	}
    
    protected function findModel($id)
    {
        if (($model = Camera::findOne($id)) !== null) {
        	return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
