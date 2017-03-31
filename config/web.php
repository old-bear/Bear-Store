<?php

$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'language' => 'zh_CN',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'vUp0plXJeLfm2XrpWpR2vAZ5JMw_tgBT',
            'enableCsrfValidation' => false,
            'parsers' => [
                'text/xml' => 'light\yii2\XmlParser',
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            // 'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'flushInterval' => 1,
            'traceLevel' => 1,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'maxFileSize' => 102400,
                    'levels' => ['error', 'warning'],
                    'except' => [
                        'yii\debug\Module::checkAccess',
                        'yii\i18n\PhpMessageSource::loadFallbackMessages',
                    ],
                    'logFile' => '@runtime/logs/store.log.wf',
                ],
                [
                    'class' => 'yii\log\FileTarget',
                    'exportInterval' => 1,
                    'maxFileSize' => 102400,
                    'levels' => ['info', 'trace'],
                    'categories' => ['service'],
                    'logFile' => '@runtime/logs/store.log',
                    'logVars' => [],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],

        'assetManager' => [
            'bundles' => [
                'yii\bootstrap\BootstrapAsset' => [
                    'sourcePath' => null,
                    'css' => [
                        '//cdn.bootcss.com/bootstrap/3.3.7/css/bootstrap.min.css',
                    ],
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'sourcePath' => null,
                    'js' => [
                        '//cdn.bootcss.com/bootstrap/3.3.7/js/bootstrap.min.js',
                    ],
                ],
                'yii\web\JqueryAsset' => [
                    'sourcePath' => null,
                    'js' => [
                        '//cdn.bootcss.com/jquery/2.2.4/jquery.min.js',
                    ],
                ],
            ],
        ],
        
        'utils' => [
            'class' => 'app\components\CommonUtility',
            //'appID' => 'wx0bc04d289116c2c1',
            //'appSecret' => '8bf38d52b4cd426c84b005c16b0ded91',
            'appID' => 'wx19722aca1cab5994',
            'appSecret' => '4349a3d633cf3cd23fcf4e77a57b0299',
            'merchantID' => '1417391302',
            'paymentKey' => '4263f100ffdf080891147980522af30e',
        ],

        'sms' => [
            'class' => 'app\components\BaiduSmsClient',
            'endPoint' => 'sms.bj.baidubce.com',
            'accessKey' => '309e987fa77f46b3ab36ff2ededab3ce',
            'secretAccessKey' => 'acc72d8e83fb440fbf763e02d57854ca',
            'invokeID' => "ByLOpIBI-NDLb-3Z3o",
        ],

        'wepay' => [
            'class' => 'app\components\WePayProxy',
            'appID' => 'wx19722aca1cab5994',
            'merchantID' => '1417391302',
            'paymentKey' => '4263f100ffdf080891147980522af30e',
        ],
    ],
    'timeZone' => 'Asia/Shanghai',
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        //'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['*'],
    ];
}

return $config;
