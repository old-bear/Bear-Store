<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Customer;

/**
 * CustomerSearch represents the model behind the search form about `app\models\Customer`.
 */
class CustomerSearch extends Customer
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'subscribe_time', 'bonus_points'], 'integer'],
            [['open_id', 'captcha', 'refresh_token', 'nick_name', 'sexuality', 'head_img_path', 'phone', 'email', 'qrcode_ticket', 'qrcode_expire_time', 'qrcode_url'], 'safe'],
            [['balance'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Customer::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'subscribe_time' => $this->subscribe_time,
            'balance' => $this->balance,
            'bonus_points' => $this->bonus_points,
            'qrcode_expire_time' => $this->qrcode_expire_time,
        ]);

        $query->andFilterWhere(['like', 'open_id', $this->open_id])
            ->andFilterWhere(['like', 'captcha', $this->captcha])
            ->andFilterWhere(['like', 'refresh_token', $this->refresh_token])
            ->andFilterWhere(['like', 'nick_name', $this->nick_name])
            ->andFilterWhere(['like', 'sexuality', $this->sexuality])
            ->andFilterWhere(['like', 'head_img_path', $this->head_img_path])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'qrcode_ticket', $this->qrcode_ticket])
            ->andFilterWhere(['like', 'qrcode_url', $this->qrcode_url]);

        return $dataProvider;
    }
}
