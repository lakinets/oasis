<?php
namespace app\components;

use yii\base\Component;
use yii\db\Connection;
use app\models\Ls;

class L2 extends Component
{
    private array $_connections = [];

    public function get(string $type, int $id): Connection
    {
        $key = "$type:$id";
        if (!isset($this->_connections[$key])) {
            $ls = Ls::findOne($id);
            if (!$ls) {
                throw new \RuntimeException("Login-сервер (id=$id) не найден.");
            }

            $this->_connections[$key] = new Connection([
                'dsn' => "mysql:host={$ls->db_host};port={$ls->db_port};dbname={$ls->db_name}",
                'username' => $ls->db_user,
                'password' => $ls->db_pass,
                'charset' => 'utf8mb4',
            ]);
        }

        return $this->_connections[$key];
    }
}