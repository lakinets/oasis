<?php

namespace app\modules\cabinet\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\db\Query;
use app\models\Gs;
use app\components\Lineage;

/**
 * Страница кабинета:
 * - /cabinet/characters?gs_id=ID — список персонажей на выбранном сервере
 * - /cabinet/characters/view?gs_id=ID&char_id=NNN — просмотр персонажа + инвентарь
 */
class CharactersController extends CabinetBaseController
{
    public function actionIndex(?int $gs_id = null)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']);
        }

        $login   = Yii::$app->user->identity->login;
        $servers = Gs::getOpenServers();

        if (!$servers) {
            return $this->render('index', [
                'servers'    => [],
                'gs_id'      => null,
                'characters' => [],
                'error'      => 'Нет активных игровых серверов.',
            ]);
        }

        if ($gs_id === null) {
            $gs_id = $servers[0]->id;
        }

        $characters = [];
        $error = null;

        try {
            $l2  = Lineage::gs($gs_id)->connect();
            $db  = $l2->getDb();

            // charactersQuery() — из динамической конфигурации (protected/l2j/<Version>.php)
            $q = $l2->charactersQuery()
                ->where(['characters.account_name' => $login]);

              $raw = $q->all($db);

           // нормализуем ключи
           $characters = [];
           foreach ($raw as $row) {
           $characters[] = [
            'char_id'    => $row['char_id'] ?? $row['obj_Id'] ?? null,
            'char_name'  => $row['char_name'] ?? null,
            'level'      => $row['level'] ?? 0,
            'clan_name'  => $row['clan_name'] ?? null,
            'online'     => $row['online'] ?? 0,
           'onlinetime' => $row['onlinetime'] ?? 0,
          ];
    }
        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            $error = 'Ошибка загрузки персонажей: ' . $e->getMessage();
        }

        return $this->render('index', [
            'servers'    => $servers,
            'gs_id'      => $gs_id,
            'characters' => $characters,
            'error'      => $error,
        ]);
    }

    public function actionView(int $gs_id, int $char_id)
    {
        if (Yii::$app->user->isGuest) {
            return $this->redirect(['/site/login']);
        }

        $login   = Yii::$app->user->identity->login;
        $servers = Gs::getOpenServers();

        // Загрузка персонажа по account_name; фильтр по char_id делаем ПОСЛЕ выборки,
        // т.к. разные ядра используют разные имена столбцов,
        // но в select динамической конфигурации мы ожидаем alias "AS char_id".
        try {
            $l2  = Lineage::gs($gs_id)->connect();
            $db  = $l2->getDb();

            $rows = $l2->charactersQuery()
                ->where(['characters.account_name' => $login])
                ->all($db);

            $character = null;
            foreach ($rows as $row) {
                if ((string)($row['char_id'] ?? '') === (string)$char_id) {
                    $character = $row;
                    break;
                }
            }

            if (!$character) {
                throw new NotFoundHttpException('Персонаж не найден или вам не принадлежит.');
            }

            // Инвентарь — универсальная схема L2J: таблица items (owner_id = char_id)
            $items = (new Query())
                ->from('items')
                ->where(['owner_id' => $char_id])
                ->all($db);

            // Пытаемся подтянуть названия предметов с БД сайта из {{%all_items}} (если есть)
            $itemsById = [];
            if ($items) {
                $itemIds = array_values(array_unique(array_map(fn($i) => (int)$i['item_id'], $items)));

                try {
                    $info = (new Query())
                        ->from('{{%all_items}}')
                        ->where(['item_id' => $itemIds])
                        ->indexBy('item_id')
                        ->all(Yii::$app->db);

                    foreach ($items as $it) {
                        $id = (int)$it['item_id'];
                        $itemsById[] = [
                            'item_id' => $id,
                            'name'    => $info[$id]['name'] ?? null,
                            'count'   => $it['count'] ?? 1,
                            'loc'     => $it['loc']   ?? null,
                            'slot'    => $it['slot']  ?? null,
                        ];
                    }
                } catch (\Throwable $e) {
                    // Таблицы нет — показываем без названий
                    foreach ($items as $it) {
                        $itemsById[] = [
                            'item_id' => (int)$it['item_id'],
                            'name'    => null,
                            'count'   => $it['count'] ?? 1,
                            'loc'     => $it['loc']   ?? null,
                            'slot'    => $it['slot']  ?? null,
                        ];
                    }
                }
            }

        } catch (\Throwable $e) {
            Yii::error($e->getMessage(), __METHOD__);
            throw new NotFoundHttpException('Ошибка загрузки данных персонажа: ' . $e->getMessage());
        }

        return $this->render('view', [
            'servers'   => $servers,
            'gs_id'     => $gs_id,
            'character' => $character,
            'items'     => $itemsById,
        ]);
    }
}
