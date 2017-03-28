<?php

namespace app\models;

use Yii;
use app\models\AccessToken; 

/**
 * This is the model class for table "access_token".
 *
 * @property integer $id
 * @property string $access_token
 * @property datetime $expire_time
 */
class JsapiTicket extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'jsapi_ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'jsapi_ticket', 'expire_time'], 'required'],
            [['id'], 'integer', 'min' => 1],
            [['jsapi_ticket'], 'string', 'max' => 512],
            [['expire_time'], 'date', 'format' => 'php:Y-m-d H:i:s'],
        ];
    }

    public static function getJsapiTicket()
    {
        $ticket = JsapiTicket::find()->orderBy('id DESC')->limit(1)->one();
        if (!$ticket) {
            $ticket = self::_refreshJsapiTicket(1);
        } else {
            $expireDate = new \DateTime($ticket->expire_time);
            $diff = $expireDate->getTimestamp() - time();
            if ($diff < 180) {
                $ticket = self::_refreshJsapiTicket($ticket->id + 1);
            }
        }
        return $ticket->jsapi_ticket;
    }

    private static function _refreshJsapiTicket(int $id)
    {
        $ak = AccessToken::getAccessToken();
        
        $resBody = Yii::$app->utils->fetchJsApiTicket($ak)['body'];
        $expireDate = (new \DateTime())->add(
            new \DateInterval('PT' . $resBody['expires_in'] . 'S')); 

        $ticket = new JsapiTicket();
        $ticket->id = $id;
        $ticket->jsapi_ticket = $resBody['ticket'];
        $ticket->expire_time = $expireDate->format('Y-m-d H:i:s');
        // TODO: Handle exception due to race condition
        $ticket->save();
        return $ticket;
    }
}
