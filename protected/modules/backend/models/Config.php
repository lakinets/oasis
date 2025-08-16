<?php
namespace app\modules\backend\models;

use Yii;
use yii\db\ActiveRecord;
use yii\helpers\Html;

class Config extends ActiveRecord
{
    public static function tableName()
    {
        return 'config';
    }

    public function rules()
    {
        return [
            [['param'], 'required'],
            [['param', 'value', 'label', 'options', 'method', 'field_type'], 'string'],
            [['param'], 'unique'],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id'    => 'ID',
            'param' => Yii::t('backend', 'Параметр'),
            'value' => Yii::t('backend', 'Значение'),
            'label' => Yii::t('backend', 'Название'),
        ];
    }

    /* ===================== Select options helpers ===================== */

    protected function yesNoOptions(): array
    {
        return ['0' => 'Нет', '1' => 'Да'];
    }

    public function getThemes(): array
    {
        $candidates = [];
        $t1 = Yii::getAlias('@themesRoot', false);
        if ($t1) $candidates[] = $t1;
        $t2 = Yii::getAlias('@app/themes', false);
        if ($t2) $candidates[] = $t2;

        $items = [];
        foreach ($candidates as $root) {
            if (is_dir($root)) {
                foreach (scandir($root) as $dir) {
                    if ($dir === '.' || $dir === '..') continue;
                    if (is_dir($root . DIRECTORY_SEPARATOR . $dir)) {
                        $items[$dir] = $dir;
                    }
                }
                if (!empty($items)) break;
            }
        }
        return empty($items) ? ['ghtweb' => 'ghtweb'] : $items;
    }

    protected function indexPageTypes(): array
    {
        return ['page' => 'Страница', 'news' => 'Новости'];
    }

    protected function pagesList(): array
    {
        return ['main' => 'main'];
    }

    protected function forumTypes(): array
    {
        return ['phpbb' => 'phpBB', 'ipb' => 'IP.Board', 'smf' => 'SMF'];
    }

    protected function gameServers(): array
    {
        return [1 => 'Server 1'];
    }

    protected function dateFormatOptions(): array
    {
        return [
            'Y-m-d H:i'     => '2025-08-11 21:30',
            'd.m.Y H:i'     => '11.08.2025 21:30',
            'd/m/Y H:i'     => '11/08/2025 21:30',
            'd.m.Y'         => '11.08.2025',
            'd/m/Y'         => '11/08/2025',
            'j F Y, H:i'    => '11 августа 2025, 21:30',
            'D, M j, Y H:i' => 'Sun, Aug 11, 2025 21:30',
            'c'             => '2025-08-11T21:30:00+03:00',
        ];
    }

    /* ===================== Поле формы для одного параметра ===================== */

    public function getField(): string
    {
        $name  = "Config[{$this->id}][value]";
        $value = (string)$this->value;

        switch ($this->param) {

            /* ---- Основное ---- */
            case 'theme':
                return Html::dropDownList($name, $value, $this->getThemes(), ['class' => 'form-select']);

            case 'index.type':
                return Html::dropDownList($name, $value, $this->indexPageTypes(), ['class' => 'form-select']);

            case 'index.page':
                return Html::dropDownList($name, $value, $this->pagesList(), ['class' => 'form-select']);

            case 'index.rss.date_format':
            case 'forum_threads.date_format':
                return Html::dropDownList($name, $value, $this->dateFormatOptions(), ['class' => 'form-select']);

            /* ---- Да/Нет ---- */
            case 'register.captcha.allow':
            case 'register.allow':
            case 'register.confirm_email':
            case 'register.multiemail':
            case 'mail.smtp':
            case 'forgotten_password.captcha.allow':
            case 'forum_threads.allow':
            case 'robokassa.test':
            case 'waytopay.sms.allow':
            case 'server_status.allow':
            case 'top.pk.allow':
            case 'top.pvp.allow':
            case 'login.captcha.allow':
            case 'referral_program.allow':
            case 'prefixes.allow':
                return Html::dropDownList($name, $value, $this->yesNoOptions(), ['class' => 'form-select']);

            /* ---- Серверы ---- */
            case 'top.pk.gs_id':
            case 'top.pvp.gs_id':
                return Html::dropDownList($name, $value, $this->gameServers(), ['class' => 'form-select']);

            /* ---- Форум ---- */
            case 'forum_threads.type':
                return Html::dropDownList($name, $value, $this->forumTypes(), ['class' => 'form-select']);

            /* ---- Числовые ---- */
            case 'index.rss.cache':
            case 'index.rss.limit':
            case 'register.confirm_email.time':
            case 'captcha.min_length':
            case 'captcha.max_length':
            case 'captcha.width':
            case 'captcha.height':
            case 'forum_threads.cache':
            case 'forum_threads.limit':
            case 'forum_threads.db_port':
            case 'robokassa.password':
            case 'robokassa.password2':
            case 'unitpay.secret_key':
            case 'unitpay.project_id':
            case 'unitpay.public_key':
            case 'waytopay.project_id':
            case 'waytopay.key':
            case 'waytopay.sms.key':
            case 'waytopay.sms.project_id':
            case 'server_status.cache':
            case 'top.pk.limit':
            case 'top.pk.cache':
            case 'top.pvp.limit':
            case 'top.pvp.cache':
            case 'login.count_failed_attempts_for_blocked':
            case 'login.failed_attempts_blocked_time':
            case 'referral_program.percent':
            case 'cabinet.referals.limit':
            case 'cabinet.transaction_history.limit':
            case 'cabinet.auth_logs_limit':
            case 'cabinet.user_messages_limit':
            case 'cabinet.tickets.limit':
            case 'cabinet.bonuses.limit':
            case 'cabinet.tickets.answers.limit':
            case 'shop.item.limit':
            case 'gallery.limit':
            case 'gallery.big.width':
            case 'gallery.big.height':
            case 'gallery.small.width':
            case 'gallery.small.height':
            case 'prefixes.length':
            case 'prefixes.count_for_list':
            case 'prefixes.count_for_list':
                return Html::textInput($name, $value, ['class' => 'form-control', 'inputmode' => 'numeric']);

            /* ---- Пароли ---- */
            case 'mail.smtp_password':
            case 'forum_threads.db_pass':
                return Html::passwordInput($name, $value, ['class' => 'form-control']);

            /* ---- Обычный текст ---- */
            default:
                return Html::textInput($name, $value, ['class' => 'form-control']);
        }
    }
}