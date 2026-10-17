<?php
namespace app\modules\install\models;

use Yii;
use yii\base\Model;
use yii\helpers\FileHelper;

class DbForm extends Model
{
    public $host = '127.0.0.1';
    public $port = 3306;
    public $db   = '';
    public $user = 'root';
    public $pass = '';

    public function rules()
    {
        return [
            [['host','port','db','user'], 'required'],
            ['port', 'integer'],
            ['pass', 'string'],
            ['pass', 'validateConnect'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'host' => Yii::t('install', 'Host'),
            'port' => Yii::t('install', 'Port'),
            'db'   => Yii::t('install', 'Database'),
            'user' => Yii::t('install', 'Username'),
            'pass' => Yii::t('install', 'Password'),
        ];
    }

    public function validateConnect($attr)
    {
        $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db}";
        try { new \PDO($dsn, $this->user, $this->pass); }
        catch (\PDOException $e) { $this->addError($attr, $e->getMessage()); }
    }

    public function writeConfig(): void
    {
        $config = "<?php
return [
    'class' => 'yii\\db\\Connection',
    'dsn' => 'mysql:host={$this->host};port={$this->port};dbname={$this->db}',
    'username' => '{$this->user}',
    'password' => '{$this->pass}',
    'charset' => 'utf8mb4',
];";
        $file = Yii::getAlias('@app/config/db.php');
        FileHelper::createDirectory(dirname($file));
        file_put_contents($file, $config);
    }
}