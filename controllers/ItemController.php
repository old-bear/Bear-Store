<?php

namespace app\controllers;

use Yii;
use app\models\Item;
use app\models\ItemSearch;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ItemController implements the CRUD actions for Item model.
 */
class ItemController extends ExternalController
{
    public $layout = 'base';

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'view' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Displays a single ItemSearch model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
        $user = $this->login();   // Favroite needs user information
        $model = ItemSearch::findOne($id);
        if ($model) {
            return $this->render('view', [
                'model' => $model,
            ]);
        } else {
            throw new NotFoundHttpException('抱歉，该商品不存在');
        }
    }
}
