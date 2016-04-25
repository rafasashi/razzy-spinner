<?php error_reporting(-1);

function loadClasses($className){
 include __DIR__."/core/classes/$className.class.php";
}//end loadclasses

spl_autoload_register('loadClasses');


include_once __DIR__."/core/vendor/autoload.php";

?>
