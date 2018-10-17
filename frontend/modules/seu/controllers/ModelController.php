<?php

namespace frontend\modules\seu\controllers;

use common\models\seu\Model;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class ModelController extends Controller
{
    public function actionView($id)
    { 	
        return $this->render('view', [
            'modelDevice' => $this->findModel($id),
        ]);
    }
    
    public function actionList($typeId, $manufacturerId)
    {
        $models = Model::find()->where(['type_id' => $typeId])->andWhere(['manufacturer_id' => $manufacturerId])->all();
        
        $out = '<option></option>';
        if(!empty($models)){
            foreach ($models as $model){
                $out .= '<option value="' . $model->id . '">' . $model->name . '</option>';
            }
        }
        
        return $out;
    }

    public function actionUpdate($id)
    {
        $modelDevice = $this->findModel($id);

        if ($modelDevice->load(Yii::$app->request->post()) && $modelDevice->save()) {
            return $this->redirect(['tree/index', 'id' => $modelDevice->id]);
        } else {
            return $this->renderAjax('update', [
                'modelDevice' => $modelDevice,
                false,
                true
            ]);
        }
    }

    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    protected function findModel($id)
    {
        if (($model = Model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
