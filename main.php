<?php

/**
 * @param $class - Type name
 * @return void
 */
function image_hash_classes_autoloader($class): void
{
    $explode = explode('\\',  $class);
    include_once 'Sources/' . $explode[count($explode) - 1] . '.php';
}

spl_autoload_register('image_hash_classes_autoloader');