<?php

namespace app\modules\cabinet\models;

use yii\base\Model;

class DepositForm extends Model
{
    public $sum = 1;

    public function rules()
    {
        return [
            ['sum', 'required'],
            ['sum', 'integer', 'min' => 1],
        ];
    }
}