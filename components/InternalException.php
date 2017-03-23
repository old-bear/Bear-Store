<?php

namespace app\components;

use Yii;
use yii\web\ServerErrorHttpException;

class InternalException extends ServerErrorHttpException
{
    function __construct(string $message = '', Exception $previous = null)
    {
        if ($message) {
            Yii::error($message);
        }
        parent::__construct("恭喜您成为第1个发现此隐藏BUG的访客，"
                            . "欢迎汇报至bugs@youtuan.com领取奖品！",
                            500, $previous);
    }
}