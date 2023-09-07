<?php
spl_autoload_register(function ($class){
    $class = str_replace('\\', DIRECTORY_SEPARATOR, $class);
    $file = __DIR__. DIRECTORY_SEPARATOR. $class. '.php';

    var_dump($file);
    if (file_exists($file)) {
        require $file;
    }
});