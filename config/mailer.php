<?php
/**
 * @copyright Copyright (c) 2016 RNP
 * @license http://github.com/ufrgs-hyman/meican#license
 */

return [
    'class' => 'yii\swiftmailer\Mailer',
    'transport' => [
        'class' => 'Swift_SmtpTransport',
        'host' => 'smtp.inf.ufrgs.br',
        'username' => 'meican@inf.ufrgs.br',
        'password' => 'Futura@2015',
        'port' => '465',
        'encryption' => 'ssl',
    ],
];
