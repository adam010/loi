<?php
require_once( "classes/class.siteform.php" );
require_once( "classes/class.leden.php" );
@session_start();
$lid=new leden;
$fields= get_object_vars($lid);
$regform=new siteform("registratie");
$regform->formfields=$fields;

//    var_dump($_POST);
if(isset($_POST['thisform'])){
    array_shift($_POST);
    array_pop($_POST);
  if($regform->processrequest($_POST))
     $lid->register($regform->formfields);
   
}



$regform->formHTML=$regform->getform($fields)
;?>
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        echo $regform->formHTML
        ?>
    </body>
</html>
