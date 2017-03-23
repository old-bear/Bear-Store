<?php

namespace app\controllers;

use Yii;
use app\models\Item;
use app\models\ItemSearch;
use yii\web\Controller;
use yii\web\UploadedFile;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ItemAdminController implements the CRUD actions for Item model.
 */
class ItemAdminController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all ItemSearch models.
     * @return mixed
     */
    public function actionIndex()
    {
//         $searchModel = new ItemSearch();
//         $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

//         return $this->render('index', [
//             'searchModel' => $searchModel,
//             'dataProvider' => $dataProvider,
//         ]);
    }

    /**
     * Displays a single ItemSearch model.
     * @param string $id
     * @return mixed
     */
    public function actionView($id)
    {
//         return $this->render('view', [
//             'model' => $this->findModel($id),
//         ]);
    }

    /**
     * Creates a new ItemSearch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
//         $model = new Item();

//         if ($model->load(Yii::$app->request->post()) && $model->save()) {
//             $model->imageFiles = UploadedFile::getInstances($model, 'imageFiles');
//             $model->uploadImages();
//             return $this->redirect(['view', 'id' => $model->id]);
//         } else {
//             return $this->render('create', [
//                 'model' => $model,
//             ]);
//         }
    }

    /**
     * Updates an existing ItemSearch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param string $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
//         $model = $this->findModel($id);

//         if ($model->load(Yii::$app->request->post()) && $model->save()) {
//             return $this->redirect(['view', 'id' => $model->id]);
//         } else {
//             return $this->render('update', [
//                 'model' => $model,
//             ]);
//         }
    }

    /**
     * Deletes an existing ItemSearch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param string $id
     * @return mixed
     */
    public function actionDelete($id)
    {
//         $this->findModel($id)->delete();

//         return $this->redirect(['index']);
    }

    /**
     * Finds the ItemSearch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param string $id
     * @return ItemSearch the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ItemSearch::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
