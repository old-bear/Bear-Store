<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access_token".
 *
 * @property integer $id
 * @property string $access_token
 * @property datetime $expire_time
 */
class AccessToken extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'access_token';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'access_token', 'expire_time'], 'required'],
            [['id'], 'integer', 'min' => 1],
            [['access_token'], 'string', 'max' => 512],
            [['expire_time'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public static function getAccessToken()
    {
        $token = AccessToken::find()->orderBy('id DESC')->limit(1)->one();
        if (!$token) {
            // id=1 stands for the first access token
            $token = self::_refreshAccessToken(1);
        } else {
            $expireDate = new \DateTime($token->expire_time);
            $diff = $expireDate->getTimestamp() - time();
            if ($diff < 180) { 
                // Refresh token when last one will expire in less than 3 minutes
                $token = self::_refreshAccessToken($token->id + 1);
            }
        }
        return $token->access_token;
    }

    private static function _refreshAccessToken(int $id)
    {
        $resBody = Yii::$app->utils->fetchAccessToken()['body'];
        $expireDate = (new \DateTime())->add(
            new \DateInterval('PT' . $resBody['expires_in'] . 'S')); 

        $token = new AccessToken();
        $token->id = $id;
        $token->access_token = $resBody['access_token'];
        $token->expire_time = $expireDate->format('Y-m-d H:i:s');
        // TODO: Handle exception due to race condition
        $token->save();
        return $token;
    }
}
