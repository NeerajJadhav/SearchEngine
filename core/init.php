<?php
/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 16-03-17
 * Time: 12:33 AM
 */
require __DIR__ . '/../vendor/autoload.php';

function alert($string){
    echo '<pre>';
    echo print_r($string,1);
    echo '</pre>';
}

spl_autoload_register(function($class) {
    require_once 'classes/' . $class . '.php';
});

