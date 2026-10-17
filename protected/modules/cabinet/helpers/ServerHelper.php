<?php
namespace app\modules\cabinet\helpers;

use Yii;
use yii\db\Connection;
use yii\db\Exception;

class ServerHelper
{
    public static function getServerConfig($gs_id)
    {
        $server = (new \yii\db\Query())
            ->from('gs')
            ->where(['id' => $gs_id])
            ->one();

        if (!$server) {
            throw new Exception(Yii::t('views', 'Сервер с ID {id} не найден в таблице gs', [
                'id' => $gs_id,
            ]));
        }

        $configPath = Yii::getAlias("@app/../protected/l2j/{$server['version']}.php");

        if (!file_exists($configPath)) {
            throw new Exception(Yii::t('views', 'Файл конфигурации {version}.php не найден', [
                'version' => $server['version'],
            ]));
        }

        return [
            'db' => [
                'dsn' => "mysql:host={$server['db_host']};port={$server['db_port']};dbname={$server['db_name']}",
                'username' => $server['db_user'],
                'password' => $server['db_pass'],
                'charset' => 'utf8',
            ],
            'info' => $server,
        ];
    }

    public static function getConnection($gs_id)
    {
        $config = self::getServerConfig($gs_id);
        return new Connection($config['db']);
    }
}
