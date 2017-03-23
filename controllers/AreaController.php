<?php

namespace app\controllers;

use Yii;
use app\models\Area;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AreaController implements the CRUD actions for Area model.
 */
class AreaController extends Controller
{
    public function actionListProvince()
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $provinces = Area::findAll(['level' => 1]);
        $ret = [];
        foreach ($provinces as $province) {
            $ret[] = ['label' => $province->name, 'value' => $province->id];
        }
        return $ret;
    }

    public function actionListCity($provinceID = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $condition = ['level' => 2];
        if ($provinceID !== null) {
            $condition['parent_id'] = intval($provinceID);
        }
        $cities = Area::findAll($condition);
        $ret = [];
        foreach ($cities as $city) {
            $ret[] = ['label' => $city->name, 'value' => $city->id];
        }
        return $ret;
    }

    public function actionListDistrict($cityID = null)
    {
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

        $condition = ['level' => 3];
        if ($cityID !== null) {
            $condition['parent_id'] = intval($cityID);
        }
        $districts = Area::findAll($condition);
        $ret = [];
        foreach ($districts as $district) {
            $ret[] = ['label' => $district->name, 'value' => $district->id];
        }
        return $ret;
    }
}
