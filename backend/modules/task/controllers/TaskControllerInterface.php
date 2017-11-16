<?php

namespace backend\modules\task\controllers;

interface TaskControllerInterface
{
	public function actionCreate($value = true);
	
	public function actionUpdate($id);
	
	public function actionDelete($id);
	
}
