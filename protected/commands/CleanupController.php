<?php
namespace app\commands;

use Yii;
use yii\console\Controller;
use app\models\PaymentTransaction;

class CleanupController extends Controller
{
    /**
     * Удаляет транзакции в статусе 'new', созданные более 24 часов назад.
     * Запуск: php yii cleanup/transactions
     */
    public function actionTransactions()
    {
        $threshold = date('Y-m-d H:i:s', time() - 86400); // 24 часа назад

        $deleted = PaymentTransaction::deleteAll([
            'and',
            ['status' => 'new'],
            ['<', 'created_at', $threshold]
        ]);

        $this->stdout("Удалено зависших транзакций: {$deleted}\n");
    }
}