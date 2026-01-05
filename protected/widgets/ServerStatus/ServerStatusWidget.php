<?php
namespace app\widgets\ServerStatus;

use yii\base\Widget;
use app\models\Gs;
use app\l2j\DriverFactory;

class ServerStatusWidget extends Widget
{
    public $compact = false;

    public function run()
    {
        $html = '';
        foreach (Gs::findOpened()->all() as $gs) {
            try {
                /* драйвер по версии из БД */
                $driver = DriverFactory::make(
                    $gs->getDb(),
                    $gs->version,
                    $gs->db_name
                );
                /* онлайн через драйвер */
                $online = $driver->getCountOnlineCharacters();
            } catch (\Throwable $e) {
                $online = 0;          // драйвер не найден / ошибка
            }

            $img = $online ? 'online' : 'offline';
            $html .= "
                <div class='server'>
                    <a href='#'>{$gs->name}</a>
                    <img src='/themes/kronos/assets/images/{$img}.png' alt=''> {$online}
                </div>";
        }
        return "<div id='status'>{$html}</div>";
    }
}