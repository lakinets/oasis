<?php

/**
 * Виджет переключения языков на сайте
 *
 * Class Languages
 */
class Languages extends CWidget
{
    public function run()
    {
        $languages = !empty(app()->params['languages']) && is_array(app()->params['languages']) && count(app()->params['languages'])
            ? app()->params['languages']
            : NULL;

        $this->render('index', array(
            'languages' => $languages,
        ));
    }
}