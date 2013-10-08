<?php
require_once( "classes/class.siteform.php" );
require_once( "classes/class.lidmaatschap.php" );
require_once( "classes/class.leden.php" );
@session_start();
$lid=new leden;
$lidmaatschap=new lidmaatschap();
$fields= $lid->nawformfields();
$regform=new siteform("registratie");
$regform->formfields=$fields;


if(isset($_POST['thisform'])){
  $form= $_POST['thisform'];
  array_shift($_POST);
  array_pop($_POST);
  if($regform->processrequest($_POST))      {
      //validate ok!
     if($form=="registratie"){ 
         if(!isset($_SESSION['aspirantlid']))
            $_SESSION['aspirantlid']=$regform->formfields;                
            
        $fields= $lidmaatschap->formfields();
        $regform=new siteform("lidmaatschap");
        $regform->formfields=$fields;      
     }
     if($form=="lidmaatschap" ){
       $lidmaatschap->update($_POST);
       $lid->save($_SESSION['aspirantlid']);
       $lidmaatschap->lidnummer=$lid->id; 
       if ($lidmaatschap->save())
             echo "succes";          
       
     }
      
  }
//    if($form=="registratie")
//        $fields= $lid->nawformfields();
//      else if($form=="lidmaatschap" )
//       $fields= $lidmaatschap->formfields();   
        
else{$fields=$_POST;}
           
           
            
}
     
       

   
//     else if($_POST['thisform']=="lidmaatschap"){     
//      $lidmaatschapDetails=$regform->formfields;   
//      $lidnummer=$lid->register($_SESSION['aspirantlid']);
//      $lidmaatschapDetails->lidnummer=$lidnummer;
//      $lidmaatschap->submit(litmaatschapDetails);
//     }
   


//$regform->formfields=$fields;
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
