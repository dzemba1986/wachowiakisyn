<?php

namespace backend\controllers;

use Yii;
use yii\web\Controller;
use backend\models\Model;

class ModelController extends Controller
{
    /**
     * Lists all Modyfication models.
     * @return mixed
     */
    public function actionView($id)
    { 	
        return $this->render('view', [
            'modelDevice' => $this->findModel($id),
        ]);
    }
    
    public function actionList($typeId, $manufacturerId)
    {
        $countModels = Model::find()->where(['type' => $typeId])->andWhere(['manufacturer' => $manufacturerId])->count();
        $models = Model::find()->where(['type' => $typeId])->andWhere(['manufacturer' => $manufacturerId])->all();
        
        echo '<option></option>';
        if($countModels > 0){
            foreach ($models as $model){
                echo '<option value="' . $model->id . '">' . $model->name . '</option>';
            }
        } 
    }

    /**
     * Updates an existing Modyfication model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
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

    /**
     * Deletes an existing Modyfication model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Modyfication model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return Modyfication the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Model::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
