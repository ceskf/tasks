<?php
spl_autoload_register(array('autoloader','loadClass'));
class autoloader{  
    public static function loadClass($class){      
        $path = $_SERVER['DOCUMENT_ROOT']."/php/class/class.$class.php"; //echo $path;
        if (is_file($path)){ 
            require_once $path;  }
    }
}
$aa = 99;
?>
