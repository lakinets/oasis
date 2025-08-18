<?php

namespace app\modules\cabinet\models;

use yii\base\Model;

class TicketsForm extends Model
{
    public $category_id;
    public $priority;
    public $title;
    public $text;

    public function rules()
    {
        return [
            [['category_id', 'priority', 'title', 'text'], 'required'],
            [['category_id', 'priority'], 'integer'],
            [['text'], 'string', 'min' => 5],
            [['title'], 'string', 'max' => 255],
        ];
    }
}