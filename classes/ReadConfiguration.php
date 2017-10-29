<?php

/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 19-03-17
 * Time: 07:07 PM
 */
class ReadConfiguration
{
    private $_config_file = '../config/engine_config.ini';

    private $_property;

    private function __construct()
    {
        $this->_property = parse_ini_file($this->_config_file);
    }

    public function getProperty($propertyName)
    {
        return $this->_property[$propertyName] ? $this->_property[$propertyName] : NULL;
    }
}