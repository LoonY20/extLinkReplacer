<?php
spl_autoload_extensions('.php');
spl_autoload_register('loadClasses');

function loadClasses($className)
{

    $className = explode('\\', $className);

    if (array_key_exists( 1, $className)) {
        if (file_exists(ROOT_CORE . DS . $className[0] . DS . $className[0] . '.php')) {
            require_once ROOT_CORE . DS . $className[0] . DS . $className[1] . '.php';
        }
    } else {
        if (file_exists(ROOT_CORE . DS . $className[0] . '.php')) {
            require_once ROOT_CORE . DS . $className[0] . '.php';
        }
    }

}