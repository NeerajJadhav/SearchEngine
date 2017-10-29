<?php
/**
 * Created by PhpStorm.
 * User: niraj
 * Date: 19-03-17
 * Time: 06:17 PM
 */

require_once '../core/init.php';

function escape($string) {
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}