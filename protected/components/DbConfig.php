<?php

class DbConfig extends CApplicationComponent
{
    protected $_data = array();
    protected $_cacheTime = 86400;



    public function init()
    {
        $dependency = new CDbCacheDependency("SELECT MAX(UNIX_TIMESTAMP(updated_at)) FROM {{config}}");

        $items = Yii::app()->db->cache($this->_cacheTime, $dependency)->createCommand('SELECT * FROM {{config}}')->queryAll();

        foreach($items as $item)
        {
            if($item['param'])
            {
                $this->_data[$item['param']] = ($item['value'] === '' ?  $item['default'] : $item['value']);
            }
        }

        parent::init();
    }

    public function get($key, $default = NULL)
    {
        return isset($this->_data[$key]) ? $this->_data[$key] : $default;
    }

    public function set($key, $value)
    {
        $command = Yii::app()->db->createCommand();

        $res = $command->update('{{config}}', array(
            'value'      => $value,
            'updated_at' => date('Y-m-d H:i:s'),
        ), 'param = :param', array('param' => $key));

        // Запись была сделана
        if($res === 1)
        {
            $this->_data[$key] = $value;
        }
    }

    public function add($params)
    {
        if(isset($params[0]) && is_array($params[0]))
        {
            foreach($params as $item)
            {
                $this->createParameter($item);
            }
        }
        elseif($params)
        {
            $this->createParameter($params);
        }
    }

    public function delete($key)
    {
        if(is_array($key))
        {
            foreach($key as $item)
            {
                $this->removeParameter($item);
            }
        }
        elseif($key)
        {
            $this->removeParameter($key);
        }
    }

    protected function createParameter($param)
    {
        if(!empty($param['param']))
        {
            $command = Yii::app()->db->createCommand();

            $res = $command->insert('{{config}}', array(
                'param'      => $param['param'],
                'label'      => isset($param['label']) ? $param['label'] : $param['param'],
                'value'      => isset($param['value']) ? $param['value'] : '',
                'default'    => isset($param['default']) ? $param['default'] : '',
                'field_type' => isset($param['field_type']) ? $param['field_type'] : 'textField',
            ));

            if($res)
            {
                $this->_data[$param['param']] = (isset($param['value']) ?  $param['value'] : (isset($param['default']) ? $param['default'] : ''));
            }
        }
    }

    protected function removeParameter($key)
    {
        if(!empty($key))
        {
            $command = Yii::app()->db->createCommand();

            $res = $command->delete('{{config}}', 'param = :param', array('param' => $key));

            if($res === 1 && isset($this->_data[$key]))
            {
                unset($this->_data[$key]);
            }
        }
    }
}
