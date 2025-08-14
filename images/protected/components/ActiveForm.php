<?php 
 
class ActiveForm extends CActiveForm
{
    public function errorSummary($models, $header = NULL, $footer = NULL, $htmlOptions = array())
    {
        $header = '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';
        $footer = '';
        $htmlOptions = array_merge(array('class' => 'alert alert-danger'), $htmlOptions);

        return parent::errorSummary($models, $header, $footer, $htmlOptions);
    }

    public static function validate($models, $attributes=null, $loadInput=true)
    {
        $result=array();
        if(!is_array($models))
            $models=array($models);
        foreach($models as $model)
        {
            $modelName=CHtml::modelName($model);
            if($loadInput && isset($_POST[$modelName]))
                $model->attributes=$_POST[$modelName];
            $model->validate($attributes);
            foreach($model->getErrors() as $attribute=>$errors)
                $result[CHtml::activeId($model,$attribute)]=$errors;
        }
        return $result;
    }
}
 