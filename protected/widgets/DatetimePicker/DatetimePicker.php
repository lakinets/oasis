<?php

/**
 * see: https://github.com/Eonasdan/bootstrap-datetimepicker
 * Class DatetimePicker
 */
class DatetimePicker extends CWidget
{
    /**
     * Поля к которым будет применен виджет
     * @var array
     */
    public $fields = array();

    /**
     * @var array
     */
    public $defaultParams = array(
        'format' => 'YYYY-MM-DD', // НЕ менять, блиать!, формат для даты и времени: YYYY-MM-DD HH:mm:ss (HH - не трогать иначе формат даты будет 12 часов + am/pm)
    );

    /**
     * @var array
     */
    public $params = array();



    public function run()
    {
        if(!$this->fields)
        {
            return;
        }

        $this->params = CMap::mergeArray($this->defaultParams, $this->params);

        $assetsUrl = Yii::app()->getAssetManager()->publish(Yii::getPathOfAlias('application.widgets.DatetimePicker.assets'), FALSE, -1, YII_DEBUG);

        Yii::app()->clientScript->registerCssFile($assetsUrl . '/css/bootstrap-datetimepicker.min.css');
        Yii::app()->clientScript->registerScriptFile($assetsUrl . '/js/moment.min.js', CClientScript::POS_END);
        Yii::app()->clientScript->registerScriptFile($assetsUrl . '/js/bootstrap-datetimepicker.js', CClientScript::POS_END);

        $scriptUniqueId = 'bootstrap-datepicker' . implode('', $this->fields);

        Yii::app()->clientScript->registerScript($scriptUniqueId, '$("' . implode(',', $this->fields) . '").datetimepicker(' . $this->getParams() . ');');
    }

    private function getParams()
    {
        $params = '{';

        foreach($this->params as $k => $v)
        {
            $v = (is_string($v) ? "'" . $v . "'" : $v);
            $v = (is_bool($v) ? strtolower(($v ? 'true' : 'false')) : $v);

            $params .= "'$k':" . $v . ",";
        }

        return substr($params, 0, -1) . '}';
    }
}
 